<?php

namespace console\jobs\subscription\stripe;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use common\models\{SubscriptionWebhook, SubscriptionHistory};
use yii\web\{ServerErrorHttpException, NotFoundHttpException};

/**
 * Class StripeCheckoutSessionCompletedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.deleted
 * @see https://stripe.com/docs/billing/subscriptions/cancel
 */
class StripeCustomerSubscriptionDeletedJob extends BaseObject implements RetryableJobInterface
{
    public array $payload;
    public int $subscriptionWebhookId;

    protected ?SubscriptionWebhook $subscriptionWebhook = null;

    /**
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        $this->setSubscriptionWebhook();

        if ($this->subscriptionWebhook->isReceived()) {
            $this->subscriptionWebhook->setProcessing();

            try {
                $this->updateSubscription();
                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $error = serialize($e);
                $this->subscriptionWebhook->setFailed(true, $error);
            }
        }
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

    protected function updateSubscription(): void
    {

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
