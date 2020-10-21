<?php

namespace frontend\models;

use Yii;
use yii\base\{
    Exception, Model
};
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use common\models\Order;

/**
 * Class OrderImport
 *
 * OrderImport is the model behind the `Import Orders` upload form.
 *
 * @package frontend\models
 *
 * @property int $customer
 */
class OrderImport extends Model
{
    public $customer;

    /** @var array Field to be exported */
    public static $csvFields = [
        'customer',
        'orderno',
        'item_sku',
        'item_quantity',
        'item_name',
        'shipto_name',
    ];

    public static $sampleData = [
        [
            'customer'      => '100',
            'orderno'       => '100006286-AMBIENT',
            'item_sku'      => 'FF55',
            'item_quantity' => '2',
            'item_name'     => 'test',
            'shipto_name'   => 'Andrew DiFeo',
        ],
        [
            'customer'      => '100',
            'orderno'       => '100006286-AMBIENT',
            'item_sku'      => 'FF57',
            'item_quantity' => '1',
            'item_name'     => 'test',
            'shipto_name'   => 'Andrew DiFeo',
        ],
    ];

    /**
     * Directory for temporarily storing uploaded file
     *
     * @var string
     */
    public static $uploadDir = "/data/uploads/leads-import/";

    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [
                ['file'],
                'file',
                'extensions'               => 'csv',
                'checkExtensionByMimeType' => false,
            ],
            [
                'customer',
                'in',
                'range' => array_keys(
                    Yii::$app->user->identity->isAdmin
                        ? Customer::getList()
                        : Yii::$app->user->identity->getCustomerList()
                ),
            ],
        ];
    }

    /**
     * Save file
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveFile()
    {
        $this->prepareUploadDirectory();

        return $this->file->saveAs($this->getFilePath());
    }

    /**
     * Prepare directory for upload
     *
     * @throws \yii\base\Exception
     */
    private function prepareUploadDirectory()
    {
        if (!is_dir(Yii::getAlias('@common') . self::$uploadDir)) {
            FileHelper::createDirectory(Yii::getAlias('@common') . self::$uploadDir, 0777, true);
        }
    }

    /**
     * Delete file
     *
     * @return bool
     */
    public function deleteFile()
    {
        return FileHelper::unlink($this->getFilePath());
    }

    /**
     * Get full path of the uploaded file
     *
     * @return string
     */
    public function getFilePath()
    {
        return Yii::getAlias('@common') . self::$uploadDir . $this->file->baseName . '.' . $this->file->extension;
    }

    /**
     * Process the import from CSV file to DB table
     *
     * This function will validate the model, temporary save the uploaded CSV file,
     * read and insert into DB, then delete the file
     *
     * @return bool
     * @throws \yii\base\Exception
     */
    public function import()
    {
        ini_set('memory_limit', '1024M');

        $userId         = Yii::$app->user->id;
        $userLocationId = Yii::$app->user->identity->currentLocation->id;

        $this->file = UploadedFile::getInstance($this, 'file');

        if (!($this->file && $this->validate())) {
            return false;
        }

        if (!$this->saveFile()) {
            $this->addError('file', 'Could not save file.');

            return false;
        }

        $attributes = MarketingLead::$exportFields;

        // Begin DB transaction
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Delete all leads for current location
            MarketingLead::deleteAll(['location_id' => $userLocationId]);

            // Open file for reading
            $file = fopen($this->getFilePath(), 'r');

            /** @var bool Lead model validation flag */
            $leadValidated = true;

            // Iterate CSV file
            $idx = 0;
            while (($line = fgetcsv($file)) !== false) {
                // Headers row
                if ($idx == 0) {
                    // Validate number of columns
                    if (count($line) !== count($attributes)) {
                        throw new Exception(
                            'Incorrect number of columns in file. CSV file must have ' .
                            count($attributes) . ' columns. Please download and use the correct template.'
                        );
                    }
                    $idx++;
                    continue; // skip headers row
                }

                $data = array_combine($attributes, $line);

                if (empty($data['id']) && empty($data['email']) && empty($data['company_name'])) {
                    // If the first three columns are empty then we consider that there is no data in this row
                    continue;
                }

                if (!is_numeric($data['id'])) {
                    unset($data['id']);
                }

                $lead = new MarketingLead($data);

                $lead->location_id = $userLocationId;
                $lead->created_on  = date("Y-m-d H:i:s");
                $lead->updated_on  = date("Y-m-d H:i:s");
                $lead->updated_by  = $userId;

                // Validate and save Address object
                if (!$lead->save()) {
                    foreach ($lead->getErrors() as $attr => $error) {
                        $msg = 'Row# ' . ($idx + 1);
                        if (isset($lead->id)) {
                            $msg .= " (ID# {$lead->id})";
                        }
                        $msg = "$msg  " . json_encode($error);
                        $this->addError('leads' . ($idx + 1) . $attr, $msg);
                    }
                    $leadValidated = false;
                    $idx++;
                    continue;
                }

                $idx++;

                $lead = $data = null;
                unset($lead, $data);
            }

            if (!$leadValidated) {
                throw new Exception(
                    'Validation errors found. Please edit and re-upload your CSV file.'
                );
            }

            // Commit DB transaction
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->addError('file', 'Import failed. ' . $e->getMessage());

            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('file', 'Import failed. ' . $e->getMessage());

            return false;
        } finally {
            if (isset($file)) {
                fclose($file);
                $this->deleteFile();
            }
        }

        return true;
    }
}
