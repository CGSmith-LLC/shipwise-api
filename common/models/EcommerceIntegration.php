<?php

namespace common\models;

use yii\helpers\Json;
use common\models\base\BaseEcommerceIntegration;

/**
 * Class EcommerceIntegration
 * @package common\models
 */
class EcommerceIntegration extends BaseEcommerceIntegration
{
    public const STATUS_INTEGRATION_CONNECTED = 1;
    public const STATUS_INTEGRATION_PAUSED = 0;

    public array $array_meta_data = [];

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

    public function disconnect(): bool|int
    {
        return $this->delete();
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

    public function isMetaKeyExistsAndNotEmpty(string $key): bool
    {
        return (isset($this->array_meta_data[$key]) && !empty($this->array_meta_data[$key]));
    }

    protected function convertMetaData(): void
    {
        if ($this->meta) {
            $this->array_meta_data = Json::decode($this->meta);
        }
    }
}
