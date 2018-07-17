<?php

namespace api\modules\v1\models\core;

use common\models\State;

/**
 * Class StateEx
 *
 * @package api\modules\v1\models\core
 */
class StateEx extends State
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "State",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "State ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "State name" ),
	 *     @SWG\Property( property = "abbreviation", type = "string",  description = "State abbreviation" ),
	 * )
	 */
	public function fields()
	{
		return ['id', 'name', 'abbreviation'];
	}
}