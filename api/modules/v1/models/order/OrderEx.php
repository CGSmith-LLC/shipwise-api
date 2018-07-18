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
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "Order",
	 *
	 *     @SWG\Property( property = "id", type = "integer", description = "Order ID" ),
	 *     @SWG\Property( property = "orderReference", type = "string", description = "Order reference" ),
	 *     @SWG\Property( property = "customerReference", type = "string", description = "Customer reference" ),
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
	public function fields()
	{
		return [
			'id'                => 'id',
			'orderReference'    => 'order_reference',
			'customerReference' => 'customer_reference',
			'shipTo'            => 'address',
			'tracking'          => 'trackingInfo',
			'items'             => 'items',
			'createdDate'       => 'created_date',
			'updatedDate'       => 'updated_date',
			'status'            => 'status',
			'history'           => 'history',
			'customer'          => 'customer',
		];
	}
}