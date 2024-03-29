<?php

namespace common\models\query;

use common\models\base\BaseBatch;
use common\models\Order;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends \yii\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \common\models\Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Query condition to get orders for given customer id
     *
     * We assume that if $id passed is null then no condition to apply
     *
     * @param int $id Customer Id
     *
     * @return OrderQuery
     */
    public function forCustomer($id)
    {
        return is_numeric($id)
            ? $this->andOnCondition([Order::tableName() . '.customer_id' => (int)$id])
            : $this;
    }

    /**
     * Query condition to get orders for multiple given customers
     *
     * @param array $ids Customer Ids
     *
     * @return OrderQuery
     */
    public function forCustomers($ids = [])
    {
        if (!empty($ids)) {
            return $this->andOnCondition([Order::tableName() . '.customer_id' => $ids]);
        }
        return $this;
    }

    /**
     * Query condition to get order by customer reference ID - identifier in the customer database
     *
     * @param string $customerReference Identifier in customer database
     *
     * @return OrderQuery
     */
    public function byCustomerReference($customerReference)
    {
        return $this->andOnCondition([Order::tableName() . '.customer_reference' => $customerReference]);
    }

    /**
     * Query condition to get orders for given status id
     *
     * @param int $id Status Id
     *
     * @return OrderQuery
     */
    public function byStatus($id)
    {
        return $this->andOnCondition([Order::tableName() . '.status_id' => (int)$id]);
    }

    public function byBatchId($id)
    {
        $batch = BaseBatch::findOne($id);

        return $this->andOnCondition(['in', Order::tableName() . '.id', array_values(ArrayHelper::map($batch->batchItems, 'id', 'order_id'))]);
    }

    /**
     * Query condition to search by origin
     *
     * @param string $origin Origin search
     *
     * @return OrderQuery
     */
    public function withOrigin($origin)
    {
        return $this->andOnCondition([Order::tableName() . '.origin' => (string)$origin]);
    }

    /**
     * Query condition to get requested ship date
     *
     * @param $date
     *
     * @return OrderQuery
     */
    public function onOrBeforeRequestedDate($date)
    {
        return $this->andWhere(['<=', Order::tableName() . '.requested_ship_date', $date]);
    }

    /**
     * Query condition to get requested ship date
     *
     * @param $date
     *
     * @return OrderQuery
     */
    public function onOrBeforeArriveByDate($date)
    {
        return $this->andWhere(['<=', Order::tableName() . '.must_arrive_by_date', $date]);
    }

    /**
     * Query condition to get orders after an updated date
     *
     * @param $date \DateTime
     *
     * @return OrderQuery
     */
    public function afterUpdatedDate($date)
    {
        return $this->andWhere(['>=', Order::tableName() . '.updated_date', $date->format('Y-m-d')]);
    }

    /**
     * Query condition to get orders after an created date
     *
     * @param $date \DateTime
     *
     * @return OrderQuery
     */
    public function afterCreatedDate($date)
    {
        return $this->andWhere(['>=', Order::tableName() . '.created_date', $date->format('Y-m-d')]);
    }

    /**
     * Query condition to get order by order id
     *
     * @param int $id Order Id
     *
     * @return OrderQuery
     */
    public function byId($id)
    {
        return $this->andWhere([Order::tableName() . '.id' => (int)$id]);
    }
}
