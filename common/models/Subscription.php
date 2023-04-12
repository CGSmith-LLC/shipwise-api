<?php

namespace common\models;

use yii\helpers\Json;
use common\models\base\BaseSubscription;
use common\models\query\SubscriptionQuery;

/**
 * Class SubscriptionHistory
 * @package common\models
 */
class Subscription extends BaseSubscription
{
    public const IS_TRUE = 1;
    public const IS_FALSE = 0;

    /**
     * @see https://stripe.com/docs/api/subscriptions/object#subscription_object-status
     */
    public const STATUS_TRIAL = 'trialing';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INCOMPLETE = 'incomplete';
    public const STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_UNPAID = 'unpaid';

    public array $array_meta_data = [];

    public static function find(): SubscriptionQuery
    {
        return new SubscriptionQuery(get_called_class());
    }

    public function init(): void
    {
        $this->on(self::EVENT_AFTER_FIND, function () {
            if ($this->meta) {
                $this->array_meta_data = Json::decode($this->meta);
            }
        });

        parent::init();
    }

    public function isActive(): bool
    {
        return $this->is_active == self::IS_TRUE;
    }

    public function isTrial(): bool
    {
        return $this->is_trial == self::IS_TRUE;
    }

    public function isPastDue(): bool
    {
        $result = false;

        if (isset($this->array_meta_data['cancel_at_period_end'])
            && (bool)$this->array_meta_data['cancel_at_period_end'] == true
            && isset($this->array_meta_data['cancel_at']) && $this->array_meta_data['cancel_at']) {
            $cancelAt = (int)$this->array_meta_data['cancel_at'];
            $result = ($cancelAt < time());
        }

        return $result;
    }

    public function makeInactive(bool $withSave = true): void
    {
        $this->is_active = self::IS_FALSE;

        if ($withSave) {
            $this->save();
        }
    }

    public function makeActive(bool $withSave = true): void
    {
        $this->is_active = self::IS_TRUE;

        if ($withSave) {
            $this->save();
        }
    }

    public function incrementUnsyncedUsageQuantity(bool $withSave = true): void
    {
        $this->unsync_usage_quantity += 1;

        if ($withSave) {
            $this->save();
        }
    }

    public function resetUnsyncedUsageQuantity(bool $withSave = true): void
    {
        $this->unsync_usage_quantity = 0;

        if ($withSave) {
            $this->save();
        }
    }
}
