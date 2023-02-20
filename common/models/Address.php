<?php

namespace common\models;

use common\models\base\BaseAddress;
use common\behaviors\OrderAddressEventsBehavior;

/**
 * Class Address
 *
 * @property string $country Country two-chars ISO code
 * @property State  $state
 *
 * @package common\models
 */
class Address extends BaseAddress
{
    public function behaviors(): array
    {
        return [
            [
                'class' => OrderAddressEventsBehavior::class,
            ],
        ];
    }

    /**
     * Get State
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne('common\models\State', ['id' => 'state_id']);
    }
}
