<?php

namespace console\jobs\subscription\stripe;

use yii\base\BaseObject;
use yii\web\NotFoundHttpException;
use yii\queue\RetryableJobInterface;
use common\models\SubscriptionWebhook;

/**
 * Class BaseStripeJob
 * @package console\jobs\subscription\stripe
 */
class BaseStripeJob extends BaseObject implements RetryableJobInterface
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

        if ($this->isExecutable()) {
            $this->subscriptionWebhook->setProcessing();
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

    protected function isExecutable(): bool
    {
        return $this->subscriptionWebhook->isReceived();
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
