<?php

namespace common\models\query;

use common\models\base\BaseBatch;
use common\models\Order;
use common\traits\LimitBy;
use yii\helpers\ArrayHelper;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderQuery extends BaseQuery
{
    use LimitBy;

    public function __construct($modelClass, $config = [])
    {
        parent::__construct($modelClass, $config);

        if (\Yii::$app instanceof \yii\web\Application &&
            \Yii::$app->id !== 'app-api' &&
            !\Yii::$app->user->identity->isAdmin) {
            // If a "warehouse" user limit orders only by the warehouse that is linked
            if (\Yii::$app->user->identity->isWarehouseType()) {
                $this->limitByWarehouse();
            }

            // If a "customer" user then limit the orders by the customers that they have access to
            if (\Yii::$app->user->identity->isCustomerType()) {
                $this->limitByCustomer();
            }
        }
    }

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
