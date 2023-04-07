<?php

namespace common\services\subscription;

use Yii;
use Stripe\Exception\SignatureVerificationException;
use Stripe\{Exception\ApiErrorException, Invoice, Product, StripeClient, Stripe, Webhook};
use Stripe\Subscription as StripeSubscription;
use frontend\models\Customer;
use common\models\Subscription;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    public const PAYMENT_METHOD_STRIPE = 'stripe';

    public const CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT = 'checkout.session.completed';
    public const CUSTOMER_SUBSCRIPTION_DELETED_WEBHOOK_EVENT = 'customer.subscription.deleted';
    public const CUSTOMER_SUBSCRIPTION_PAUSED_WEBHOOK_EVENT = 'customer.subscription.paused';
    public const CUSTOMER_SUBSCRIPTION_RESUMED_WEBHOOK_EVENT = 'customer.subscription.resumed';
    public const CUSTOMER_SUBSCRIPTION_UPDATED_WEBHOOK_EVENT = 'customer.subscription.updated';
    public const CUSTOMER_SUBSCRIPTION_TRIAL_WILL_END = 'customer.subscription.trial_will_end';
    public const CUSTOMER_DELETED_WEBHOOK_EVENT = 'customer.deleted';

    protected Customer $customer;
    protected StripeClient $stripeClient;

    protected ?Subscription $activeSubscription = null;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
        $this->stripeClient = new StripeClient(Yii::$app->params['stripe']['secret_key']);
    }

    ###############
    ## Getters: ##
    ##############

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getActiveSubscription(): Subscription|null
    {
        if (!$this->activeSubscription) {
            $this->activeSubscription = Subscription::find()
                ->where([
                    'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
                    'customer_id' => $this->customer->id,
                    'is_active' => Subscription::IS_TRUE
                ])
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }

        return $this->activeSubscription;
    }

    public function getSubscriptionByPaymentMethodSubscriptionId(string $paymentMethodSubscriptionId): Subscription|null
    {
        /**
         * @var $subscription Subscription
         */
        $subscription = Subscription::find()
            ->where([
                'payment_method_subscription_id' => $paymentMethodSubscriptionId,
                'customer_id' => $this->customer->id
            ])
            ->one();

        return $subscription;
    }

    public function getAllSubscriptions(array $orderBy = ['id' => SORT_DESC]): array|null
    {
        return Subscription::find()
            ->where([
                'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
                'customer_id' => $this->customer->id
            ])
            ->orderBy($orderBy)
            ->all();
    }

    ##############
    ## History: ##
    ##############

    public function addSubscription(array $params): bool|Subscription
    {
        $subscription = new Subscription($params);
        return ($subscription->save()) ? $subscription : false;
    }

    public function updateSubscription(string $paymentMethodSubscriptionId, array $params): bool|Subscription
    {
        /**
         * @var $subscription Subscription
         */
        $subscription = Subscription::find()
            ->where([
                'payment_method_subscription_id' => $paymentMethodSubscriptionId,
                'customer_id' => $this->customer->id
            ])
            ->one();

        if ($subscription) {
            $subscription->attributes = $params;
            return ($subscription->save()) ? $subscription : false;
        }

        return false;
    }

    public function makeSubscriptionsInactive(?Subscription $currentActiveSubscription = null): void
    {
        $allSubscriptions = $this->getAllSubscriptions();

        /**
         * @var $subscription Subscription
         */
        foreach ($allSubscriptions as $subscription) {
            if ($currentActiveSubscription && $subscription->id != $currentActiveSubscription->id) {
                $subscription->makeInactive();
            }
        }
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
    public function getSubscriptionObjectById(string $id): StripeSubscription
    {
        return $this->stripeClient->subscriptions->retrieve($id);
    }

    /**
     * @throws ApiErrorException
     */
    public function getInvoiceObjectById(string $id): Invoice
    {
        return $this->stripeClient->invoices->retrieve($id);
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
