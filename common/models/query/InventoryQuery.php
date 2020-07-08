<?php


namespace common\models\query;


use common\models\Inventory;

class InventoryQuery extends \yii\db\ActiveQuery
{

    /**
     * Query condition to get orders for given customer id
     *
     * We assume that if $id passed is null then no condition to apply
     *
     * @param int $id Customer Id
     *
     * @return InventoryQuery
     */
    public function forCustomer($id)
    {
        return is_numeric($id)
            ? $this->andOnCondition([Inventory::tableName() . '.customer_id' => (int)$id])
            : $this;
    }
}