<?php

namespace common\models;

use common\models\base\BasePackageItem;

/**
 * Class Item
 *
 * @package common\models
 */
class PackageItem extends BasePackageItem
{

    /**
     * Get package items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLotInfo()
    {
        return $this->hasMany('common\models\PackageItemLotInfo', ['package_items_id' => 'id']);
    }
}