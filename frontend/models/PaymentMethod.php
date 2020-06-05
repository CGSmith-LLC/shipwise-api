<?php

namespace frontend\models;


use Stripe\SetupIntent;
use Stripe\StripeClient;
use Yii;

/**
 * This is the model class for table "payment-method".
 *
 */
class PaymentMethod extends \common\models\PaymentMethod
{
    /** @var string Used for stripe form */
    public $card_number;
    public $card_month;
    public $card_year;
    public $card_cvc;

    public function rules()
    {
        $rules =  parent::rules();
        $otherRules = [
            [['card_number', 'card_month', 'card_year', 'card_cvc'], 'integer'],
        ];

        return array_merge($rules, $otherRules);
    }

    /**
     * Create payment method on Stripe then attach ID to the model
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $stripe = new StripeClient(\Yii::$app->stripe->privateKey);

        $paymentMethod = $stripe->customers->createSource(
            $this->customer->stripe_customer_id,
            ['source' => [
                'object' => 'card',
                'number' => $this->card_number,
                'exp_month' => $this->card_month,
                'exp_year' => $this->card_year,
                'cvc' => $this->card_cvc,
            ]]
        );

        return parent::beforeSave($insert);
    }

    public function init()
    {
        parent::init();

        $this->setAttribute('customer_id', Yii::$app->user->identity->getCustomerId());
        $this->setupIntent = SetupIntent::create([
            'customer' => Yii::$app->user->identity->getCustomerStripeId(),
        ]);
    }


}
