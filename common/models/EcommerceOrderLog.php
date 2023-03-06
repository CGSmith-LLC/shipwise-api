<?php

namespace common\models;

use yii\helpers\Json;
use common\models\base\BaseEcommerceOrderLog;

/**
 * Class EcommerceOrderLog
 * @package common\models
 */
class EcommerceOrderLog extends BaseEcommerceOrderLog
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_SUCCESS => 'Success',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    public static function success(EcommerceIntegration $ecommerceIntegration, array $payload, Order $order): void
    {
        $ecommerceOrderLog = new EcommerceOrderLog();
        $ecommerceOrderLog->platform_id = $ecommerceIntegration->ecommercePlatform->id;
        $ecommerceOrderLog->integration_id = $ecommerceIntegration->id;
        $ecommerceOrderLog->original_order_id = (string)$payload['id'];
        $ecommerceOrderLog->internal_order_id = $order->id;
        $ecommerceOrderLog->status = self::STATUS_SUCCESS;
        $ecommerceOrderLog->payload = Json::encode($payload, JSON_PRETTY_PRINT);
        $ecommerceOrderLog->save();
    }

    public static function failed(EcommerceIntegration $ecommerceIntegration, array $payload, ?array $meta = null): void
    {
        $ecommerceOrderLog = new EcommerceOrderLog();
        $ecommerceOrderLog->platform_id = $ecommerceIntegration->ecommercePlatform->id;
        $ecommerceOrderLog->integration_id = $ecommerceIntegration->id;
        $ecommerceOrderLog->original_order_id = (string)$payload['id'];
        $ecommerceOrderLog->status = self::STATUS_FAILED;
        $ecommerceOrderLog->payload = Json::encode($payload, JSON_PRETTY_PRINT);
        $ecommerceOrderLog->meta = ($meta) ? Json::encode($meta, JSON_PRETTY_PRINT) : null;
        $ecommerceOrderLog->save();
    }
}
