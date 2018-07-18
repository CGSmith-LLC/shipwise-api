<?php

namespace common\models;

use common\models\base\BaseOrder;

/**
 * Class Order
 *
 * @package common\models
 */
class Order extends BaseOrder
{
	/**
	 * Get Customer
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer()
	{
		return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
	}

	/**
	 * Get Ship To Address
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getAddress()
	{
		return $this->hasOne('common\models\Address', ['id' => 'address_id']);
	}

	/**
	 * Get Tracking Info
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getTrackingInfo()
	{
		return $this->hasOne('common\models\TrackingInfo', ['id' => 'tracking_id']);
	}

	/**
	 * Get Order Items
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getItems()
	{
		return $this->hasMany('common\models\Item', ['order_id' => 'id']);
	}

	/**
	 * Get Order Status
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatus()
	{
		return $this->hasOne('common\models\Status', ['id' => 'status_id']);
	}

	/**
	 * Get Order History
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getHistory()
	{
		return $this->hasMany('common\models\OrderHistory', ['order_id' => 'id']);
	}
}