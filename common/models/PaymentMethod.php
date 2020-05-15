<?php

namespace common\models;

use frontend\models\PaymentMethods;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\SetupIntent;
use Stripe\Source;
use Stripe\Stripe;
use Yii;
use yii\base\Event;
use yii\queue\serializers\IgbinarySerializer;

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
    const PRIMARY_PAYMENT_METHOD_NO = 0;
    const PRIMARY_PAYMENT_METHOD_YES = 1;


    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        $this->setAttribute('customer_id', Yii::$app->user->identity->getCustomerId());
        // Set API Key for stripe
        Stripe::setApiKey(Yii::$app->stripe->privateKey);
        $this->setupIntent = SetupIntent::create([
            'customer' => Yii::$app->user->identity->getCustomerStripeId(),
        ]);

        // Configure events to call Stripe
        $this->on(self::EVENT_BEFORE_DELETE, [$this, 'stripeDetachSource']);
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'checkForPrimaryPayment']);
        // $this->on(self::EVENT_BEFORE_INSERT, [$this, 'stripeCreateSource']);
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
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * If primary payment is selected... mark all current payment_methods.primary as false first
     *
     * @param $event
     */
    public function checkForPrimaryPayment($event)
    {
        if ($event->sender->default) {
            $paymentMethods = PaymentMethod::find()->where([
                'customer_id' => $event->sender->customer_id,
                'default' => self::PRIMARY_PAYMENT_METHOD_YES
            ])->all();

            /** @var PaymentMethod $paymentMethod */
            foreach ($paymentMethods as $paymentMethod) {
                $paymentMethod->default = self::PRIMARY_PAYMENT_METHOD_NO;
                $paymentMethod->save();
            }
        }
    }

    /**
     * @param $event Event
     * @throws ApiErrorException
     */
    public function stripeCreateSource($event)
    {
       // if (!$event->sender->account_type) {
           // $customer = (isset($event->sender->subscriber->stripe_customer_token)) ? $event->sender->subscriber->stripe_customer_token : $event->sender->developer->stripe_customer_token;

            // Create new bank source
          /**$source = Customer::createSource(
                $customer,
                [
                    'source' => $event->sender->stripe_token, // Bank token
                ]
            );*/

            /** @var $source Source */
            Yii::debug($event);
            $this->setAttribute('stripe_payment_method_id', isset($source->id) ? $source->id : null);

            // Credit card payment method
            try {
                $paymentMethod = \Stripe\PaymentMethod::retrieve($event->sender->stripe_payment_method_id);

                // Associate with customer id
                $paymentMethod->attach(['customer' => $event->sender->customer->stripe_customer_token]);

                /** @var $paymentMethod \Stripe\PaymentMethod */
                $this->setAttribute('stripe_token', isset($paymentMethod->id) ? $paymentMethod->id : null);
            } catch (ApiErrorException $e) {
                $event->sender->addError('cc', $e->getMessage());
                $event->isValid = false;
            }
    }
}

