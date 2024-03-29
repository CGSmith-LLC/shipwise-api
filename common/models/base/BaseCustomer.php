<?php

namespace common\models\base;

use Aws\Result;
use common\models\Customer;
use common\models\PaymentMethod;
use Stripe\Event;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Yii;
use yii\base\ErrorException;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;
use \bpsys\yii2\aws\s3\traits\S3MediaTrait;


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

        Stripe::setApiKey(\Yii::$app->stripe->privateKey);
//        if (!YII_ENV_DEV) {
//             Configure events to call Stripe
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'stripeCreate']);
        $this->on(self::EVENT_BEFORE_UPDATE, [$this, 'stripeUpdate']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'stripeDelete']);
    }
//    }

    /**
     * Call stripe to create the customer and set our attribute to the stripe token
     *
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function stripeCreate($event)
    {
        try {
            $customer = \Stripe\Customer::create([
                'name' => $event->sender->name,
            ]);

            /** @var $customer Customer */
            $this->setAttribute('stripe_customer_id', $customer->id);
        } catch (ErrorException $e) {
            Yii::error($e);
            throw new ServerErrorHttpException('Failed to create stripe customer');

        }
    }

    /**
     * @param $event Event
     * @throws ApiErrorException
     */
    public function stripeUpdate($event)
    {
        try {
            \Stripe\Customer::update(
                $event->sender->stripe_customer_id,
                [
                    'name' => $event->sender->name,
                ]);
        } catch (ErrorException $e) {
            Yii::error($e);
            throw new ServerErrorHttpException('Customer not updated on stripe');
        }
    }


    /**
     * @param $event Event
     * @throws ApiErrorException
     */
    public function stripeDelete($event)
    {
        try {
            $customer = \Stripe\Customer::retrieve($event->sender->stripe_customer_id);
            $customer->delete();
        } catch (ErrorException $e) {
            Yii::error($e);
            throw new NotFoundHttpException('Customer not found on stripe');
        }
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
     * Payment method relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, ['customer_id' => 'id']);
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
