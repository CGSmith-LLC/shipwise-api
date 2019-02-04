<?php

namespace api\modules\v1\models\order;

use common\models\Order;

/**
 * Class OrderEx
 *
 * @package api\modules\v1\models\order
 */
class OrderEx extends Order
{
	/**
	 * @SWG\Definition(
	 *     definition = "Order",
	 *
	 *     @SWG\Property( property = "id", type = "integer", description = "Order ID" ),
	 *     @SWG\Property( property = "uuid", type = "string", description = "Reference to ecommerce UUID" ),
	 *     @SWG\Property( property = "carrier_id", type = "integer", description = "Specifies Carrier to ship through" ),
	 *     @SWG\Property( property = "service_id", type = "integer", description = "Specifies Service level to ship through" ),
	 *     @SWG\Property( property = "orderReference", type = "string", description = "Order reference - Order number from fulfillment side" ),
	 *     @SWG\Property( property = "customerReference", type = "string", description = "Customer reference - Order Number from ecommerce side" ),
	 *     @SWG\Property( property = "requestedShipDate", type = "string", format = "date-time", description = "When the order should ship and be fulfilled" ),
	 *     @SWG\Property( property = "shipTo", ref = "#/definitions/Address" ),
	 *     @SWG\Property( property = "tracking", ref = "#/definitions/TrackingInfo" ),
	 *	   @SWG\Property(
	 *          property = "items",
	 *          type = "array",
	 *     		@SWG\Items( ref = "#/definitions/Item" )
	 *     ),
	 *     @SWG\Property( property = "createdDate", type = "string", format = "date-time" ),
	 *     @SWG\Property( property = "updatedDate", type = "string", format = "date-time" ),
	 *     @SWG\Property( property = "status", ref = "#/definitions/Status" ),
	 *     @SWG\Property( property = "history", ref = "#/definitions/OrderHistory" ),
	 *     @SWG\Property( property = "customer", ref = "#/definitions/Customer" ),
	 * )
	 */

	/**
	 * {@inheritdoc}
	 */
	public function fields()
	{
		return [
			'id'                => 'id',
			'uuid'              => 'uuid',
			'orderReference'    => 'order_reference',
			'customerReference' => 'customer_reference',
			'requestedShipDate' => 'requested_ship_date',
			'shipTo'            => 'address',
			'tracking'          => 'trackingInfo',
			'items'             => 'items',
			'createdDate'       => 'created_date',
			'updatedDate'       => 'updated_date',
			'status'            => 'status',
			'history'           => 'history',
			'customer'          => 'customer',
			'carrier_id'        => 'carrier_id',
			'service_id'        => 'service_id',
		];
	}

	/**
	 * Get Customer
	 *
	 * Overwrite parent method to use CustomerEx
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCustomer()
	{
		return $this->hasOne('api\modules\v1\models\customer\CustomerEx', ['id' => 'customer_id']);
	}

	/**
	 * Get Order Status
	 *
	 * Overwrite parent method to use StatusEx
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatus()
	{
		return $this->hasOne('api\modules\v1\models\order\StatusEx', ['id' => 'status_id']);
	}

	/**
	 * Get Order History
	 *
	 * Overwrite parent method to use OrderHistoryEx
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getHistory()
	{
		return $this->hasMany('api\modules\v1\models\order\OrderHistoryEx', ['order_id' => 'id']);
	}

	/**
	 * Get Items
	 *
	 * Overwrite parent method to use OrderHistoryEx
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getItems()
	{
		return $this->hasMany('api\modules\v1\models\order\ItemEx', ['order_id' => 'id']);
	}

	/**
	 * Get Ship To Address
	 *
	 * Overwrite parent method to use AddressEx
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getAddress()
	{
		return $this->hasOne('api\modules\v1\models\core\AddressEx', ['id' => 'address_id']);
	}

	/**
	 * Get Tracking Info
	 *
	 * @todo This method is done in prevision of the future implementation of TrackingInfo relation.
	 *       As for now, and before that transition happen, this method will imitate the return of a TrackingInfoEx
	 *       object but only `TrackingInfoEx.trackingNumber` property will be populated.
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getTrackingInfo()
	{
		// Uncomment this line to return TrackingInfoEx relation, and remove everything after this line.
		// return $this->hasOne('api\modules\v1\models\order\TrackingInfoEx', ['id' => 'tracking_id']);

		$trackingInfo           = new TrackingInfoEx();
		$trackingInfo->tracking = $this->tracking;
		return $trackingInfo;
	}
}