<?php

namespace api\modules\v1\models\order;

use common\models\OrderHistory;

/**
 * Class OrderHistoryEx
 *
 * @package api\modules\v1\models\order
 */
class OrderHistoryEx extends OrderHistory
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "OrderHistory",
	 *
	 *     @SWG\Property( property = "id", type = "integer", description = "Order history ID" ),
	 *     @SWG\Property( property = "status", ref = "#/definitions/Status" ),
	 *     @SWG\Property( property = "createdDate", type = "string", format = "date-time" ),
	 *     @SWG\Property( property = "comment", type = "string", description = "Order history comment" ),
	 * )
	 */
	public function fields()
	{
		return [
			'id'          => 'id',
			'status'      => 'status',
			'createdDate' => 'created_date',
			'comment'     => 'comment',
		];
	}
}