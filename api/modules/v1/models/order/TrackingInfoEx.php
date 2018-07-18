<?php

namespace api\modules\v1\models\order;

use common\models\TrackingInfo;

/**
 * Class TrackingInfoEx
 *
 * @package api\modules\v1\models\order
 */
class TrackingInfoEx extends TrackingInfo
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "TrackingInfo",
	 *
	 *     @SWG\Property( property = "id", type = "integer", description = "Tracking Info ID" ),
	 *     @SWG\Property( property = "service", ref = "#/definitions/Service" ),
	 *     @SWG\Property( property = "tracking", type = "string", description = "Carrier's tracking number" ),
	 *     @SWG\Property( property = "createdDate", type = "string", format = "date-time" ),
	 * )
	 */
	public function fields()
	{
		return [
			'id'          => 'id',
			'service'     => 'service',
			'tracking'    => 'tracking',
			'createdDate' => 'created_date',
		];
	}
}