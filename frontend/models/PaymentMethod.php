<?php

namespace frontend\models;


use frontend\controllers\BillingController;
use Stripe\SetupIntent;
use Stripe\StripeClient;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

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

        try {
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
            $this->stripe_payment_method_id = $paymentMethod->id;
        } catch (\Exception $e) {
            $this->addError('stripe', $e->getMessage());
            return false;

        }

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

    public function brandImage()
    {
        if ($this->brand == 'visa') {
            return '<img src= /images/card_brands/visa.png>';
        } elseif ($this->brand == 'discover') {
            return '<img src= /images/card_brands/discover_logo.jpg>';
        } elseif ($this->brand == 'master_card') {
            return '<img src= /images/card_brands/mc_vrt_opt_pos_63_2x.png>';
        } elseif ($this->brand == 'american_express') {
            return '<img src= /images/card_brands/Amex_logo_color.png>';
        } else
            return '<img src= /images/card_brands/discover_logo.jpg>';
    }


}
