<?php


namespace common\models\query;


use common\models\Invoice;

class InvoiceQuery extends \yii\db\ActiveQuery
{

    /**
     * Query condition to get orders for given customer id
     *
     * We assume that if $id passed is null then no condition to apply
     *
     * @param int $id Customer Id
     *
     * @return InvoiceQuery
     */
    public function forCustomer($id)
    {
        return is_numeric($id)
            ? $this->andOnCondition([Invoice::tableName() . '.customer_id' => (int)$id])
            : $this;
    }

    /**
     * Query condition to get orders for multiple given customers
     *
     * @param array $ids Customer Ids
     *
     * @return InvoiceQuery
     */
    public function forCustomers($ids = [])
    {
        if (!empty($ids)) {
            return $this->andOnCondition([Invoice::tableName() . '.customer_id' => $ids]);
        }
        return $this;
    }

}