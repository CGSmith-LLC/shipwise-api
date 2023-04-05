<?php

namespace common\models;

use common\models\base\BaseSubscriptionWebhook;
use common\services\subscription\SubscriptionService;
use console\jobs\subscription\stripe\StripeCheckoutSessionCompletedJob;
use console\jobs\subscription\stripe\StripeCustomerSubscriptionDeletedJob;
use yii\helpers\Json;

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

        switch ($this->event) {
            case SubscriptionService::CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT:
                \Yii::$app->queue->push(
                    new StripeCheckoutSessionCompletedJob($jobDataArray)
                );
                break;
            case SubscriptionService::CUSTOMER_SUBSCRIPTION_DELETED_WEBHOOK_EVENT:
                \Yii::$app->queue->push(
                    new StripeCustomerSubscriptionDeletedJob($jobDataArray)
                );
                break;
        }
    }
}
