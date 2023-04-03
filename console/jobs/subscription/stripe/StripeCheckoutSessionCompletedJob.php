<?php

namespace console\jobs\subscription\stripe;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use yii\web\NotFoundHttpException;
use common\models\SubscriptionWebhook;
use frontend\models\Customer;

/**
 * Class StripeCheckoutSessionCompletedJob
 * @package console\jobs\subscription\stripe
 * @see https://stripe.com/docs/api/events/types#event_types-checkout.session.completed
 * @see https://stripe.com/docs/payments/checkout/fulfill-orders#fulfill
 * @see https://stripe.com/docs/no-code/pricing-table#handle-fulfillment-with-the-stripe-api
 */
class StripeCheckoutSessionCompletedJob extends BaseObject implements RetryableJobInterface
{
    public array $payload;
    public int $subscriptionWebhookId;

    protected ?SubscriptionWebhook $subscriptionWebhook = null;
    protected ?Customer $customer = null;

    /**
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        $this->setSubscriptionWebhook();
        $this->setCustomer();

        var_dump($this->payload);
        var_dump($this->customer->id);

        $this->subscriptionWebhook->setSuccess();
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setSubscriptionWebhook(): void
    {
        $subscriptionWebhook = SubscriptionWebhook::findOne($this->subscriptionWebhookId);

        if (!$subscriptionWebhook) {
            throw new NotFoundHttpException('Subscription webhook not found.');
        }

        $this->subscriptionWebhook = $subscriptionWebhook;
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setCustomer(): void
    {
        $customer = Customer::findOne((int)$this->payload['data']['object']['client_reference_id']);

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $this->customer = $customer;
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }

    public function getTtr(): int
    {
        return 5 * 60;
    }
}
