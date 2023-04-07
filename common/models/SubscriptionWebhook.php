<?php

namespace common\models;

use Yii;
use yii\helpers\Json;
use common\models\base\BaseSubscriptionWebhook;
use common\services\subscription\SubscriptionService;
use console\jobs\subscription\stripe\{StripeCheckoutSessionCompletedJob,
    StripeCustomerDeletedJob,
    StripeCustomerSubscriptionDeletedJob,
    StripeCustomerSubscriptionPausedJob,
    StripeCustomerSubscriptionResumedJob,
    StripeCustomerSubscriptionTrialWillEndJob,
    StripeCustomerSubscriptionUpdatedJob};

class SubscriptionWebhook extends BaseSubscriptionWebhook
{
    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function init(): void
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'createJob']);
    }

    #############
    # Statuses: #
    #############

    public static function getStatuses(): array
    {
        return [
            self::STATUS_RECEIVED => 'Received',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public function isReceived(): bool
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isSuccess(): bool
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setReceived(bool $withSave = true): void
    {
        $this->status = self::STATUS_RECEIVED;

        if ($withSave) {
            $this->save();
        }
    }

    public function setProcessing(bool $withSave = true): void
    {
        $this->status = self::STATUS_PROCESSING;

        if ($withSave) {
            $this->save();
        }
    }

    public function setSuccess(bool $withSave = true): void
    {
        $this->status = self::STATUS_SUCCESS;

        if ($withSave) {
            $this->save();
        }
    }

    public function setFailed(bool $withSave = true, string $meta = null): void
    {
        $this->status = self::STATUS_FAILED;
        $this->meta = $meta;

        if ($withSave) {
            $this->save();
        }
    }

    #########
    # Jobs: #
    #########

    protected function createJob(): void
    {
        $jobDataArray = [
            'payload' => Json::decode($this->payload),
            'subscriptionWebhookId' => $this->id
        ];

        $jobObject = match ($this->event) {
            SubscriptionService::CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT => new StripeCheckoutSessionCompletedJob($jobDataArray),
            SubscriptionService::CUSTOMER_SUBSCRIPTION_DELETED_WEBHOOK_EVENT => new StripeCustomerSubscriptionDeletedJob($jobDataArray),
            SubscriptionService::CUSTOMER_SUBSCRIPTION_PAUSED_WEBHOOK_EVENT => new StripeCustomerSubscriptionPausedJob($jobDataArray),
            SubscriptionService::CUSTOMER_SUBSCRIPTION_RESUMED_WEBHOOK_EVENT => new StripeCustomerSubscriptionResumedJob($jobDataArray),
            SubscriptionService::CUSTOMER_SUBSCRIPTION_UPDATED_WEBHOOK_EVENT => new StripeCustomerSubscriptionUpdatedJob($jobDataArray),
            SubscriptionService::CUSTOMER_DELETED_WEBHOOK_EVENT => new StripeCustomerDeletedJob($jobDataArray),
            SubscriptionService::CUSTOMER_SUBSCRIPTION_TRIAL_WILL_END => new StripeCustomerSubscriptionTrialWillEndJob($jobDataArray),
            default => null,
        };

        if ($jobObject) {
            Yii::$app->queue->push($jobObject);
        }
    }
}
