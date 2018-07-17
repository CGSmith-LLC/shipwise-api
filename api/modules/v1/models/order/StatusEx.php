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
	 * @inheritdoc
	 * @todo Add constants with status codes (enum)
	 *
	 * @SWG\Definition(
	 *     definition = "Status",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Status ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Status name" ),
	 * )
	 */
	public function fields()
	{
		return ['id', 'name'];
	}
}