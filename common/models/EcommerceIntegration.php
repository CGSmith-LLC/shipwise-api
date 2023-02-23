<?php

namespace common\models;

use common\models\base\BaseEcommerceIntegration;

/**
 * Class EcommerceIntegration
 * @package common\models
 */
class EcommerceIntegration extends BaseEcommerceIntegration
{
    public const STATUS_INTEGRATION_DISCONNECTED = 0;
    public const STATUS_INTEGRATION_CONNECTED = 1;
    public const STATUS_INTEGRATION_PAUSED = -1;

    public static function getStatuses(): array
    {
        return [
            self::STATUS_INTEGRATION_DISCONNECTED => 'Disconnected',
            self::STATUS_INTEGRATION_CONNECTED => 'Connected',
            self::STATUS_INTEGRATION_PAUSED => 'Paused',
        ];
    }

    public function isConnected(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_CONNECTED;
    }

    public function isDisconnected(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_DISCONNECTED;
    }

    public function isPaused(): bool
    {
        return $this->status === self::STATUS_INTEGRATION_PAUSED;
    }
}
