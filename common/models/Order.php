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
	 * @todo This method is done in prevision of the future implementation of TrackingInfo relation.
	 *       As for now, and before that transition happen, this method will imitate the return of a TrackingInfo object
	 *       but only `TrackingInfo.tracking` property will be populated.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getTrackingInfo()
	{
		// Uncomment this line to return TrackingInfo relation, and remove everything after this line.
		// return $this->hasOne('common\models\TrackingInfo', ['id' => 'tracking_id']);

		$trackingInfo           = new TrackingInfo();
		$trackingInfo->tracking = $this->tracking;
		return $trackingInfo;
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