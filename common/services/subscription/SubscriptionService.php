<?php

namespace common\services\subscription;

use Yii;
use Stripe\Exception\SignatureVerificationException;
use Stripe\{Exception\ApiErrorException, Product, StripeClient, Stripe, Subscription, Webhook};
use frontend\models\Customer;
use common\models\SubscriptionHistory;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    public const PAYMENT_METHOD_STRIPE = 'stripe';
    public const CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT = 'checkout.session.completed';

    protected Customer $customer;
    protected StripeClient $stripeClient;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->stripeClient = new StripeClient(Yii::$app->params['stripe']['secret_key']);
    }

    ###########################
    ## Getters and checkers: ##
    ###########################

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function hasActiveSubscription(): bool
    {
        return 0;
    }

    public function getActiveSubscription()
    {
        return SubscriptionHistory::find()
            ->where([
                'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
                'customer_id' => $this->customer->id,
                'is_active' => SubscriptionHistory::IS_TRUE
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    ##########
    ## API: ##
    ##########

    /**
     * @throws ApiErrorException
     */
    public function getProductObjectById(string $id): Product
    {
        return $this->stripeClient->products->retrieve($id);
    }

    /**
     * @throws ApiErrorException
     */
    public function getSubscriptionObjectById(string $id): Subscription
    {
        return $this->stripeClient->subscriptions->retrieve($id);
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

    ##############
    ## History: ##
    ##############

    public function addSubscriptionHistory(array $params): bool
    {
        $subscriptionHistory = new SubscriptionHistory($params);
        return $subscriptionHistory->save();
    }
}
