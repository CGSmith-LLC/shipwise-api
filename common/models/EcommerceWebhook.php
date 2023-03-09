<?php

namespace common\models;

use common\models\base\BaseEcommerceWebhook;
use common\traits\MetaDataFieldTrait;

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
}
