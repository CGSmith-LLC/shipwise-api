<?php

namespace console\jobs\speedee;

use common\models\Customer;
use common\models\CustomerMeta;
use Yii;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use \common\models\SpeedeeManifest;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Csv\Writer;
use Carbon\Carbon;

class SpeeDeeShipJob extends BaseObject implements RetryableJobInterface
{
    public int $customer_id;
    public int $customer_number;

    /**
     * Get the current index for the specific customer. If none exists, start it at 0000.
     *
     * @return string
     */
    private function getIndex(): string
    {
        return Yii::$app->cache->getOrSet('speedee_manifest_index_' . $this->customer_number, function () {
            return '0000';
        });
    }

    /**
     * Advance the index to the next 4-digit combination.
     *
     * @return void
     */
    private function bumpIndex() : void
    {
        // Retrieve current string value, coerce to int (under threat of violence), advance.
        $current = intval(Yii::$app->cache->get('speedee_manifest_index_' . $this->customer_number));
        $current++;
        // Set the 4-digit value.
        Yii::$app->cache->set('speedee_manifest_index_' . $this->customer_number, sprintf('%04d', $current));
    }

    /**
     * Update the given record with the sent manifest data
     *
     * @param SpeedeeManifest $manifest
     * @param $filename
     * @param $checksum
     * @return void
     */
    private function updateManifestEntry(SpeedeeManifest $manifest, $filename, $checksum): void
    {
        $manifest->manifest_filename = $filename;
        $manifest->is_manifest_sent = true;
        $manifest->checksum = $checksum;
        $manifest->save();
    }

    public function execute($queue)
    {
        // I'm sure you made some nifty abstraction for this so just shoehorn it in here
        $this->customer_number = CustomerMeta::find()->where(['customer_id' => $this->customer_id])->andWhere(['key' => 'speedee_customer_number'])->one()->value;

        // Roll up qualifying manifest entries as an array
        $manifests = SpeedeeManifest::find()
            ->where(['customer_id' => $this->customer_id])
            ->andWhere(['is_manifest_sent' => false])
            ->all();

        if (count($manifests) == 0) {
            return;
        }

        $filename = $this->customer_number
            . '.'
            . Carbon::now()->format('Ymd')
            . $this->getIndex();

        // Format and write to a single CSV
        $csv = Writer::createFromString();
        foreach ($manifests as $manifest) {
            $manifest = $manifest->toArray();
            $formattedManifest = [];
            foreach ($manifest as $key => $value) {
                $formattedManifest[] = $value;
            }
            $csv->insertOne($formattedManifest);
            unset($formattedManifest);
        }

        // Calculate a sha1 checksum
        $checksum = sha1($csv->toString());

        // Set up the connection
        $adapter = new FtpAdapter(
            FtpConnectionOptions::fromArray([
                'host' => Yii::$app->params['speedeeFtpHost'],
                'root' => '/',
                'username' => Yii::$app->params['speedeeFtpUser'],
                'password' => Yii::$app->params['speedeeFtpPass'],
                'port' => 21,
                'ssl' => false,
                'timeout' => 90,
                'utf8' => false,
                'passive' => true,
                'transferMode' => FTP_BINARY,
            ])
        );

        $filesystem = new Filesystem($adapter);

        // Write to the remote server
        try {
            Yii::info('trying?');
            $filesystem->write($filename, $csv->toString());
        } catch (\League\Flysystem\FilesystemException $e) {
            // whoopsie
        }

        // Validate remote file
        try {
            $validate = sha1($filesystem->read($filename));
            if ($validate !== $checksum) {
                throw new \Exception('Manifest ' . $filename . ' did not pass checksum.');
            }
        } catch (\League\Flysystem\FilesystemException $e) {
            // uh oh
        } catch (\Exception $e) {
            // oopsadoodle
        }

        // Update the manifest entries in the application database
        foreach ($manifests as $manifest) {
            $this->updateManifestEntry($manifest, $filename, $checksum);
        }

        // Advance the current filename in the local cache.
        $this->bumpIndex();
    }

    public function getTtr()
    {
        return 120;
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 3) && ($error instanceof \League\Flysystem\FilesystemException) || ($error instanceof Exception);
    }
}