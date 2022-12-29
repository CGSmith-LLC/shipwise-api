<?php

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use \common\models\SpeedeeManifest;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Carbon\Carbon;

class SpeeDeeShipJob extends BaseObject implements RetryableJobInterface
{
    public SpeedeeManifest $manifest;
    public int $index;
    public function execute($queue)
    {
        $temp = fopen('php://temp/maxmemory:1048576', 'w');
        fputcsv($temp, $this->manifest->toArray());

        $filename = $this->manifest->bill_to_shipper_number
            . Carbon::now()->format('Ymd')
            . $this->index;

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
            // @TODO: handle exception
        }

        fclose($temp);

        // Validate remote file
        try {
            $validate = sha1($filesystem->read('/' . $filename));
            if ($validate !== $checksum) {
                throw new Exception('Manifest ' . $filename . ' did not pass checksum.');
            }
        } catch (\League\Flysystem\FilesystemException $e) {
            // @TODO: handle filesystem exception
        } catch (Exception $e) {
            // @TODO: handle general exception
        }

        $this->manifest->manifest_filename = $filename;
        $this->manifest->is_manifest_sent = true;
        $this->manifest->checksum = $checksum;
        $this->manifest->save();


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