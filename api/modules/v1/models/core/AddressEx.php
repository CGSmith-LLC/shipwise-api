<?php

namespace api\modules\v1\models\core;

use common\models\Address;

/**
 * Class AddressEx
 *
 * @package api\modules\v1\models\core
 */
class AddressEx extends Address
{
	/**
	 * @inheritdoc
	 *
	 * @SWG\Definition(
	 *     definition = "Address",
	 *
	 *     @SWG\Property( property = "id",   type = "integer", description = "Address ID" ),
	 *     @SWG\Property( property = "name", type = "string", description = "Contact name" ),
	 *     @SWG\Property( property = "address1", type = "string",  description = "Address line 1" ),
	 *     @SWG\Property( property = "address2", type = "string",  description = "Address line 2" ),
	 *     @SWG\Property( property = "city", type = "string",  description = "City" ),
	 *     @SWG\Property( property = "state", ref = "#/definitions/State" ),
	 *     @SWG\Property( property = "zip", type = "string",  description = "ZIP / Postal Code" ),
	 *     @SWG\Property( property = "phone", type = "string",  description = "Phone number" ),
	 *     @SWG\Property( property = "notes", type = "string",  description = "Notes" ),
	 * )
	 */
	public function fields()
	{
		return [
			'id'       => 'id',
			'name'     => 'name',
			'address1' => 'address1',
			'address2' => 'address2',
			'city'     => 'city',
			'state'    => 'state',
			'zip'      => 'zip',
			'phone'    => 'phone',
			'notes'    => 'notes',
		];
	}
}