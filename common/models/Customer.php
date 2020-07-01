<?php

namespace common\models;

use common\models\base\BaseCustomer;
use Stripe\Stripe;

/**
 * Class Customer
 *
 * @property string $country Country two-chars ISO code
 *
 * @package common\models
 */
class Customer extends BaseCustomer
{

    public $country = 'US';

    public function init()
    {
        parent::init();

        Stripe::setApiKey(\Yii::$app->stripe->privateKey);

    }

    /**
     * Call stripe to create the customer and set our attribute to the stripe token
     *
     * @throws \Stripe\Exception\ApiErrorException
     * @return void
     */
    public function stripeCreate()
    {
        $customer = \Stripe\Customer::create([
            'name' => $this->name,
        ]);

        /** @var $customer Customer */
        $this->setAttribute('stripe_customer_id', $customer->id);
    }

}
