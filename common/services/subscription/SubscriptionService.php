<?php

namespace common\services\subscription;

use Yii;
use Stripe\Exception\SignatureVerificationException;
use Stripe\{Invoice, Product, Stripe, UsageRecord, Webhook};
use Stripe\Subscription as StripeSubscription;
use frontend\models\Customer;
use common\models\Subscription;
use yii\web\ServerErrorHttpException;

/**
 * Class SubscriptionService
 * @package common\services\subscription
 */
class SubscriptionService
{
    public const PAYMENT_METHOD_STRIPE = 'stripe';

    public const CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT = 'checkout.session.completed';
    public const CUSTOMER_SUBSCRIPTION_CREATED_WEBHOOK_EVENT = 'customer.subscription.created';
    public const CUSTOMER_SUBSCRIPTION_DELETED_WEBHOOK_EVENT = 'customer.subscription.deleted';
    public const CUSTOMER_SUBSCRIPTION_UPDATED_WEBHOOK_EVENT = 'customer.subscription.updated';
    public const CUSTOMER_DELETED_WEBHOOK_EVENT = 'customer.deleted';

    protected Customer $customer;

    protected ?Subscription $activeSubscription = null;

    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
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
                    'is_active' => 1
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

    ####################
    ## Subscriptions: ##
    ####################

    public function isSubscriptionExists(string $paymentMethodSubscriptionId): bool
    {
        return Subscription::find()->where([
            'payment_method_subscription_id' => $paymentMethodSubscriptionId
        ])->exists();
    }

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

    #################
    ## Stripe API: ##
    #################

    public function getProductObjectById(string $id): Product
    {
        return Yii::$app->stripe->client->products->retrieve($id);
    }

    public function getSubscriptionObjectById(string $id): StripeSubscription
    {
        return Yii::$app->stripe->client->subscriptions->retrieve($id);
    }

    public function getInvoiceObjectById(string $id): Invoice
    {
        return Yii::$app->stripe->client->invoices->retrieve($id);
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function updateSubscriptionUsage(Subscription $subscription): UsageRecord
    {
        if (isset($subscription->array_meta_data['items']['data'][0])) {
            $id = $subscription->array_meta_data['items']['data'][0]['id'];
            $params = [
                'quantity' => $subscription->unsync_usage_quantity,
                'timestamp' => time(),
                'action' => 'increment',
            ];

            $res = Yii::$app->stripe->client->subscriptionItems->createUsageRecord($id, $params);

            if ($res) {
                $subscription->resetUnsyncedUsageQuantity();
            }

            return $res;
        } else {
            throw new ServerErrorHttpException('Subscription item is invalid.');
        }
    }

    ###############
    ## Webhooks: ##
    ###############

    /**
     * @throws SignatureVerificationException
     */
    public static function isWebhookVerified(string $payload, string $signatureHeader): bool
    {
        Stripe::setApiKey(Yii::$app->stripe->privateKey);
        $endpointSecret = Yii::$app->stripe->webhookSigningSecret;

        $event = Webhook::constructEvent($payload, $signatureHeader, $endpointSecret);

        return (bool)$event;
    }
}
