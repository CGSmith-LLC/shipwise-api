<?php

namespace common\models;

use Yii;
use common\models\base\BaseEcommerceWebhook;
use common\traits\MetaDataFieldTrait;
use common\services\platforms\ShopifyService;
use console\jobs\platforms\webhooks\shopify\{ShopifyAppUninstalledJob,
    ShopifyCustomerDataRequestJob,
    ShopifyCustomerRedactJob,
    ShopifyOrderCancelledJob,
    ShopifyOrderCreatedJob,
    ShopifyOrderDeletedJob,
    ShopifyOrderFulfilledJob,
    ShopifyOrderPaidJob,
    ShopifyOrderPartiallyFulfilledJob,
    ShopifyOrderUpdatedJob,
    ShopifyShopRedactJob};

/**
 * Class EcommerceWebhook
 * @package common\models
 */
class EcommerceWebhook extends BaseEcommerceWebhook
{
    use MetaDataFieldTrait;

    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public function init(): void
    {
        parent::init();
        $this->on(self::EVENT_AFTER_FIND, [$this, 'convertMetaData']);
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

    public function createJob(): bool
    {
        if ($this->isReceived() && $this->isJobExecutable()) {
            switch ($this->platform->name) {
                case EcommercePlatform::SHOPIFY_PLATFORM_NAME:
                    $this->createJobForShopify();
                    break;
            }

            //$this->setProcessing();

            return true;
        }

        return false;
    }

    protected function isJobExecutable(): bool
    {
        // All webhook events from all E-commerce platforms:
        $availableEvents = array_merge(
            ShopifyService::$webhookListeners,
            ShopifyService::$mandatoryWebhookListeners
        );

        if ($this->platform->isActive() && in_array($this->event, $availableEvents) && $this->payload) {
            return true;
        }

        return false;
    }

    protected function createJobForShopify()
    {
        $job = match ($this->event) {
            'orders/create' => new ShopifyOrderCreatedJob(['ecommerceWebhookId' => $this->id]),
            'orders/cancelled' => new ShopifyOrderCancelledJob(['ecommerceWebhookId' => $this->id]),
            'orders/updated' => new ShopifyOrderUpdatedJob(['ecommerceWebhookId' => $this->id]),
            'orders/delete' => new ShopifyOrderDeletedJob(['ecommerceWebhookId' => $this->id]),
            'orders/fulfilled' => new ShopifyOrderFulfilledJob(['ecommerceWebhookId' => $this->id]),
            'orders/partially_fulfilled' => new ShopifyOrderPartiallyFulfilledJob(['ecommerceWebhookId' => $this->id]),
            'orders/paid' => new ShopifyOrderPaidJob(['ecommerceWebhookId' => $this->id]),
            'app/uninstalled' => new ShopifyAppUninstalledJob(['ecommerceWebhookId' => $this->id]),
            'customers/data_request' => new ShopifyCustomerDataRequestJob(['ecommerceWebhookId' => $this->id]),
            'customers/redact' => new ShopifyCustomerRedactJob(['ecommerceWebhookId' => $this->id]),
            'shop/redact' => new ShopifyShopRedactJob(['ecommerceWebhookId' => $this->id]),
        };

        if (isset($job)) {
            Yii::$app->queue->push($job);
        }
    }
}
