<?php

namespace common\models\query;

use common\models\Order;

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