<?php

namespace common\models;

use common\models\base\BaseSubscription;
use yii\helpers\Json;

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
}
