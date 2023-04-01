<?php

namespace common\services\subscription;

use Yii;
use Stripe\StripeClient;
use common\models\Customer;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    protected Customer $customer;
    protected StripeClient $stripeClient;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->stripeClient = new StripeClient(Yii::$app->params['stripe']['secret_key']);
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function hasActiveSubscription(): bool
    {
        return 0;
    }

    /**
     * @see https://stripe.com/docs/billing/subscriptions/build-subscriptions?ui=elements#create-customer
     */
    public function createStripeCustomer()
    {

    }
}
