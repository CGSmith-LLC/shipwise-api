<?php

namespace common\models;

use common\models\base\BaseSubscriptionWebhook;
use common\services\subscription\SubscriptionService;
use console\jobs\subscription\stripe\StripeCheckoutSessionCompletedJob;
use yii\helpers\Json;

class SubscriptionWebhook extends BaseSubscriptionWebhook
{
    public const PAYMENT_METHOD_STRIPE = 'stripe';

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

    public function setFailed(bool $withSave = true): void
    {
        $this->status = self::STATUS_FAILED;

        if ($withSave) {
            $this->save();
        }
    }

    #########
    # Jobs: #
    #########

    protected function createJob(): void
    {
        $arrayPayload = Json::decode($this->payload);

        switch ($this->event) {
            case SubscriptionService::CHECKOUT_SESSION_COMPLETED_WEBHOOK_EVENT:
                \Yii::$app->queue->push(
                    new StripeCheckoutSessionCompletedJob([
                        'payload' => $arrayPayload,
                        'subscriptionWebhookId' => $this->id
                    ])
                );
                break;
        }
    }
}
