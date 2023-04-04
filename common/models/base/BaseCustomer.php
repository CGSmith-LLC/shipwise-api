<?php

namespace common\models\base;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property string $name
 * @property string $address1 Address line 1
 * @property string $address2 Address line 2
 * @property string $city     City
 * @property int $state_id State ID
 * @property string $zip      ZIP code
 * @property string $phone    Phone number
 * @property string $email    Email address
 * @property UploadedFile $logo     The absolute URL of the logo
 * @property string $stripe_customer_id   Stripe ID for the customer
 * @property string $created_date
 * @property int $direct Is this customer paying or not?
 *
 * @property \common\models\State $state
 */
class BaseCustomer extends \yii\db\ActiveRecord
{

    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
    }

    public function init()
    {
        parent::init();
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'created_date'], 'safe'],
            [['state_id', 'id'], 'integer'],
            [['name'], 'string', 'max' => 45],
            [['address1', 'address2', 'city'], 'string', 'max' => 64],
            [['zip'], 'string', 'max' => 16],
            [['phone'], 'string', 'max' => 32],
            [['stripe_customer_id'], 'string', 'max' => 128],
            [['email', 'logo'], 'string', 'max' => 255],
            [['direct'], 'integer'],
            [['imageFile'], 'image', 'extensions' => 'png, jpg', 'maxWidth' => 250, 'maxHeight' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'city' => 'City',
            'state_id' => 'State ID',
            'zip' => 'Zip',
            'phone' => 'Phone',
            'email' => 'Email',
            'logo' => 'Logo',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Get State
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne('common\models\State', ['id' => 'state_id']);
    }

    /**
     * File Upload for logo
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function upload()
    {
        /*gets received file from user and saves it to digital ocean space*/
        if ($this->validate()) {
            $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
            if (isset($this->imageFile)) {
                /** @var \bilberrry\spaces\Service $storage */
                $storage = Yii::$app->get('storage');
                $storage->commands()->upload($this->id . '-' . uniqid() . '-' . $this->imageFile, $this->imageFile->tempName)->execute();
                /* creates a url string varibale to return to get stored as the logo in datarbase */
                return $storage->getUrl($this->imageFile);
            }
        } else {
            return false;
        }
    }
}
