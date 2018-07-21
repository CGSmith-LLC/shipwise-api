<?php

namespace api\modules\v1\models\order;

use common\models\Status;

/**
 * Class StatusEx
 *
 * @package api\modules\v1\models\order
 */
class StatusEx extends Status
{
	/**
	 * @SWG\Definition(
	 *     definition = "Status",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Status ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Status name" ),
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