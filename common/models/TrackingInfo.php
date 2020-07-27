<?php

namespace common\models;

use common\models\base\BaseTrackingInfo;

/**
 * Class TrackingInfo
 *
 * @package common\models
 */
class TrackingInfo extends BaseTrackingInfo
{

    /**
     * Get Service
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne('common\models\shipping\Service', ['id' => 'service_id']);
    }
}