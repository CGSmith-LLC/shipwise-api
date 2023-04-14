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
        return (bool) $this->is_active;
    }

    public function isTrial(): bool
    {
        return (bool) $this->is_trial;
    }

    public function isPastDue(): bool
    {
        return !empty($this->array_meta_data['cancel_at_period_end'])
            && !empty($this->array_meta_data['cancel_at'])
            && (int)$this->array_meta_data['cancel_at'] < time();
    }

    public function makeInactive(bool $withSave = true): void
    {
        $this->is_active = 0;

        if ($withSave) {
            $this->save();
        }
    }

    public function makeActive(bool $withSave = true): void
    {
        $this->is_active = 1;

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
