<?php

namespace common\services\subscription;

use Yii;
use Stripe\Exception\SignatureVerificationException;
use Stripe\{StripeClient, Stripe, Webhook};
use common\models\Customer;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    public const CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT = 'checkout.session.completed';

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

    ###############
    ## Webhooks: ##
    ###############

    /**
     * @throws SignatureVerificationException
     */
    public static function isWebhookVerified(string $payload, string $signatureHeader): bool
    {
        Stripe::setApiKey(Yii::$app->params['stripe']['secret_key']);
        $endpointSecret = Yii::$app->params['stripe']['webhook_signing_secret'];

        $event = Webhook::constructEvent($payload, $signatureHeader, $endpointSecret);

        return (bool)$event;
    }
}
