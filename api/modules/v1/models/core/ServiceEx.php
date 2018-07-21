<?php

namespace api\modules\v1\models\core;

use common\models\Service;

/**
 * Class ServiceEx
 *
 * @package api\modules\v1\models\core
 */
class ServiceEx extends Service
{
	/**
	 * @SWG\Definition(
	 *     definition = "Service",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Service ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Service name" ),
	 *     @SWG\Property( property = "carrier", ref = "#/definitions/Carrier" ),
	 * )
	 */

	/**
	 * {@inheritdoc}
	 */
	public function fields()
	{
		return ['id', 'name', 'carrier'];
	}
}