<?php

namespace api\modules\v1\models\core;

use common\models\Carrier;

/**
 * Class CarrierEx
 *
 * @package api\modules\v1\models\core
 */
class CarrierEx extends Carrier
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "Carrier",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Carrier ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Carrier name" ),
	 * )
	 */
	public function fields()
	{
		return ['id', 'name'];
	}
}