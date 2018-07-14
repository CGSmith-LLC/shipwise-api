<?php

namespace api\modules\v1\models\forms;

use api\modules\v1\models\core\ApiConsumerEx;
use yii\base\Model;

/**
 * Class AuthenticationForm
 *
 * @package api\modules\v1\models
 *
 * @SWG\Definition(
 *     definition = "AuthenticationForm",
 *     required   = { "key", "secret" },
 *     @SWG\Property( property = "key", type = "string", ),
 *     @SWG\Property( property = "secret", type = "string" ),
 * )
 */
class AuthenticationForm extends Model
{
	const SUCCESS            = 1;
	const ERR_MISSING_FIELDS = "ERR_MISSING_FIELDS";
	const ERR_AUTH_FAILURE   = "ERR_AUTH_FAILURE";
	const ERR_INACTIVE       = "ERR_USER_INACTIVE";

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
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			["key", "required", "message" => 'Required fields are missing. "{attribute}"'],
			["secret", "required", "message" => 'Required fields are missing. "{attribute}"'],
		];
	}

	/**
	 * @return int|string
	 * @throws \yii\web\ServerErrorHttpException
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