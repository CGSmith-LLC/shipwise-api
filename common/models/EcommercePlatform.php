<?php

namespace common\models;

use common\models\base\BaseEcommercePlatform;
use yii\db\ActiveQuery;

/**
 * Class EcommercePlatform
 * @package common\models
 */
class EcommercePlatform extends BaseEcommercePlatform
{
    public const STATUS_PLATFORM_ACTIVE = 1;
    public const STATUS_PLATFORM_INACTIVE = 0;

    public const SHOPIFY_PLATFORM_NAME = 'Shopify';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PLATFORM_ACTIVE => 'Active',
            self::STATUS_PLATFORM_INACTIVE => 'Inactive',
        ];
    }

    public function getEcommerceIntegration(): ActiveQuery
    {
        return $this->hasOne(EcommerceIntegration::class, ['platform_id' => 'id']);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_PLATFORM_ACTIVE;
    }

    public function switchStatus(): void
    {
        $this->status = ($this->isActive()) ? self::STATUS_PLATFORM_INACTIVE : self::STATUS_PLATFORM_ACTIVE;
        $this->save();
    }

    /**
     * TODO: implement this later
     */
    public function getConnectedUsersCounter(): int
    {
        return 0;
    }

    public static function getShopifyObject(): ?EcommercePlatform
    {
        return self::findOne(['name' => self::SHOPIFY_PLATFORM_NAME]);
    }
}
