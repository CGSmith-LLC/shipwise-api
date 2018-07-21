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
	 * @SWG\Definition(
	 *     definition = "Carrier",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Carrier ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Carrier name" ),
	 * )
	 */

	/**
	 * {@inheritdoc}
	 */
	public function fields()
	{
		return ['id', 'name'];
	}
}