<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use common\models\base\BaseEcommerceIntegration;
use console\jobs\NotificationJob;
use common\traits\MetaDataFieldTrait;
use common\services\platforms\ShopifyService;

/**
 * Class EcommerceIntegration
 * @package common\models
 */
class EcommerceIntegration extends BaseEcommerceIntegration
{
    use MetaDataFieldTrait;

    public const STATUS_INTEGRATION_CONNECTED = 1;
    public const STATUS_INTEGRATION_PAUSED = 0;
    public const STATUS_INTEGRATION_UNINSTALLED = -1;

    public function init(): void
    {
        parent::init();
        $this->on(self::EVENT_AFTER_FIND, [$this, 'convertMetaData']);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_INTEGRATION_CONNECTED => 'Connected',
            self::STATUS_INTEGRATION_PAUSED => 'Paused',
            self::STATUS_INTEGRATION_UNINSTALLED => 'Uninstalled',
        ];
    }

    public function isConnected(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_CONNECTED;
    }

    public function isPaused(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_PAUSED;
    }

    public function isUninstalled(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_UNINSTALLED;
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function disconnect(): bool|int
    {
        switch ($this->ecommercePlatform->name) {
            case EcommercePlatform::SHOPIFY_PLATFORM_NAME:

                $shopifyService = new ShopifyService($this->array_meta_data['shop_url'], $this);
                $shopifyService->deleteWebhookListenersJob();

                break;
        }

        return $this->delete();
    }

    public function uninstall(bool $withNotification = false): void
    {
        $this->status = self::STATUS_INTEGRATION_UNINSTALLED;
        $this->save();

        if ($withNotification) {
            $shopUrl = Html::encode($this->array_meta_data['shop_url']);
            $subject = '⚠️ Problem pulling data from ' . $shopUrl;
            $message = 'We were not able to pull data from the Shopify shop ' . $shopUrl .'.';
            $message .= ' The status of the shop is changed to `Uninstalled`.';
            $message .= ' Please click the link below and try to reconnect the shop.';

            Yii::$app->queue->push(
                new NotificationJob([
                    'customer_id' => $this->customer_id,
                    'subject' => $subject,
                    'message' => $message,
                    'url' => ['/ecommerce-integration/index'],
                    'urlText' => 'Reconnect the shop',
                ])
            );
        }
    }

    public function pause(): bool
    {
        $this->status = self::STATUS_INTEGRATION_PAUSED;
        return $this->save();
    }

    public function resume(): bool
    {
        $this->status = self::STATUS_INTEGRATION_CONNECTED;
        return $this->save();
    }
}
