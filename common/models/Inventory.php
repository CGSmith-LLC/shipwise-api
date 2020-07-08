<?php

namespace common\models;

use common\models\base\BaseInventory;
use common\models\query\InventoryQuery;

/**
 * Class Inventory
 *
 * @package common\models
 *
 */
class Inventory extends BaseInventory
{

    /**
     * @inheritdoc
     * @return InventoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InventoryQuery(get_called_class());
    }

    /**
     * Get Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

}
