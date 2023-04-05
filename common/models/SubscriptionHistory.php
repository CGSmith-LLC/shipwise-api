<?php

namespace common\models;

use common\models\base\BaseSubscriptionHistory;
use yii\helpers\Json;

/**
 * Class SubscriptionHistory
 * @package common\models
 */
class SubscriptionHistory extends BaseSubscriptionHistory
{
    public const IS_TRUE = 1;
    public const IS_FALSE = 0;

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
