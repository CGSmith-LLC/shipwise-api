<?php

namespace common\models;

use Stripe\SetupIntent;
use Stripe\Stripe;
use Yii;

/**
 * This is the model class for table "paymentmethod".
 *
 * @property int $id
 * @property int $customer_id Reference to customer
 * @property string $stripe_payment_method_id
 * @property int $default Is this the customer's default payment method?
 */
class PaymentMethod extends \yii\db\ActiveRecord
{

    /**@var $setupIntent setupIntent */
    public $setupIntent;


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        // Set API Key for stripe
        Stripe::setApiKey(Yii::$app->stripe->privateKey);
        $this->setupIntent = SetupIntent::create();

        // Configure events to call Stripe
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'checkForPrimaryPayment']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'stripeCreateSource']);
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'stripeDetachSource']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'paymentmethod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'default'], 'integer'],
            [['default'], 'required'],
            [['stripe_payment_method_id'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'stripe_payment_method_id' => 'Stripe Payment Method ID',
            'default' => 'Default',
        ];
    }

    /**
     * Relation for customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id'=> 'customer_id']);
    }
}
