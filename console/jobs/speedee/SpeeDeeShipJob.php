<?php

namespace console\jobs\speedee;

use Yii;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use \common\models\SpeedeeManifest;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use League\Csv\Writer;
use Carbon\Carbon;

class SpeeDeeShipJob extends BaseObject implements RetryableJobInterface
{
    public SpeedeeManifest $manifest;
    public int $customer_id;

    /**
     * Get the current index for the specific customer. If none exists, start it at 0000.
     *
     * @return string
     */
    private function getIndex(): string
    {
        return Yii::$app->cache->getOrSet('speedee_manifest_index_' . $this->manifest->ship_from_shipper_number, function () {
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
        $current = intval(Yii::$app->cache->get('speedee_manifest_index_' . $this->manifest->ship_from_shipper_number));
        $current++;
        // Set the 4-digit value.
        Yii::$app->cache->set('speedee_manifest_index_' . $this->manifest->ship_from_shipper_number, sprintf('%04d', $current));
    }

    public function execute($queue)
    {
        $manifests = SpeedeeManifest::find()
            ->where(['customer_id' => $this->customer_id])
            ->andWhere(['is_manifest_sent' => false])
            ->asArray()
            ->all();

        $temp = fopen('php://temp/maxmemory:1048576', 'w');
        $filename = $this->manifest->bill_to_shipper_number
            . '.'
            . Carbon::now()->format('Ymd')
            . $this->getIndex();

        $csv = Writer::createFromString();


        foreach ($manifests as $manifest) {
            $formattedManifest = [];
            foreach ($manifest as $key => $value) {
                $formattedManifest[] = $value;
            }
            $csv->insertOne($formattedManifest);
            unset($formattedManifest);
        }

        file_put_contents($temp, $csv->toString());

        $checksum = sha1($temp);

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
                'systemType' => null, // 'windows' or 'unix'
                'ignorePassiveAddress' => null, // true or false
                'timestampsOnUnixListingsEnabled' => false, // true or false
                'recurseManually' => true // true
            ])
        );

        $filesystem = new League\Flysystem\Filesystem($adapter);

        try {
            $filesystem->write('/' . $filename, $temp);
        } catch (\League\Flysystem\FilesystemException $e) {
            // whoopsie
        }

        fclose($temp);

        // Validate remote file
        try {
            $validate = sha1($filesystem->read('/' . $filename));
            if ($validate !== $checksum) {
                throw new \Exception('Manifest ' . $filename . ' did not pass checksum.');
            }
        } catch (\League\Flysystem\FilesystemException $e) {
            // uh oh
        } catch (\Exception $e) {
            // oopsadoodle
        }

        Yii::$app->db->beginTransaction();

        $this->manifest->manifest_filename = $filename;
        $this->manifest->is_manifest_sent = true;
        $this->manifest->checksum = $checksum;
        $this->manifest->save();

        Yii::$app->db->endTransaction();

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