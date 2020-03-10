<?php

namespace common\models;

use common\models\base\BasePackage;

/**
 * Class Package
 *
 * @package common\models
 *
 * @property PackageItem[] $items
 */
class Package extends BasePackage
{


    /**
     * Get package items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('common\models\PackageItem', ['package_id' => 'id']);
    }
}