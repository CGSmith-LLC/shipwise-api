<?php

namespace api\modules\v1\models\forms;

use api\modules\v1\models\core\ApiConsumerEx;
use yii\base\Model;

///**
// * @deprecated Not used since Basic Auth was implemented to replace API authentication.
// * @ SWG\Definition(
// *     definition = "AuthenticationForm",
// *     required   = { "key", "secret" },
// *     @ SWG\Property( property = "key", type = "string", description = "Your API key" ),
// *     @ SWG\Property( property = "secret", type = "string", description = "Your API secret" ),
// * )
// */

/**
 * Class AuthenticationForm
 *
 * @deprecated Not used since Basic Auth was implemented to replace API authentication.
 *
 * @package api\modules\v1\models\forms
 */
class AuthenticationForm extends Model
{
	const SUCCESS            = 1;
	const ERR_MISSING_FIELDS = 2;
	const ERR_AUTH_FAILURE   = 3;
	const ERR_INACTIVE       = 4;

	public $key;
	public $secret;

	/**
	 * @var ApiConsumerEx
	 */
	private $_apiConsumer;

	/**
	 * @return ApiConsumerEx
	 */
	public function getApiConsumer()
	{
		return $this->_apiConsumer;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			["key", "required", "message" => '{attribute} is required.'],
			["secret", "required", "message" => 'Field "{attribute}" is required.'],
		];
	}

	/**
	 * Authenticate user
	 *
	 * @return int|string
	 * @throws \yii\base\Exception
	 */
	public function authenticate()
	{
		// Find api consumer by key/secret
		if (($this->_apiConsumer = ApiConsumerEx::findByKeySecret($this->key, $this->secret)) === null) {
			return self::ERR_AUTH_FAILURE;
		}

		// Check if user is active, if not, then throw an error
		if (!$this->_apiConsumer->isActive()) {
			return self::ERR_INACTIVE;
		}

		// Generate and save auth token
		$this->_apiConsumer->generateToken()->save();

		// Return success
		return self::SUCCESS;
	}

}