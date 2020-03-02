<?php

namespace common\models;

use common\models\base\BaseAddress;

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

    public $country = 'US';

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
