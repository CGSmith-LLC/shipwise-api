<?php

namespace common\services\subscription;

use Stripe\StripeClient;
use common\models\Customer;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    protected Customer $customer;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
