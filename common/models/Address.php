<?php

namespace common\models;

use common\models\base\BaseAddress;

/**
 * Class Address
 *
 * @package common\models
 */
class Address extends BaseAddress
{

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