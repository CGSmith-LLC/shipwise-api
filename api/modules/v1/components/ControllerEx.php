<?php

namespace api\modules\v1\components;

use api\modules\v1\models\core\ApiConsumerEx;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;

/**
 * Class ControllerEx
 *
 * @package api\modules\v1\components
 */
class ControllerEx extends Controller
{
	/** @var  \yii\web\Request */
	public $request;

	/** @var  \yii\web\Response */
	public $response;

	/**
	 * API Consumer
	 *
	 * @var \api\modules\v1\models\core\ApiConsumerEx
	 */
	public $apiConsumer;

	/** @inheritdoc */
	public function init()
	{
		parent::init();

		$this->request = Yii::$app->request;
		$this->response = Yii::$app->response;
	}

	/** @inheritdoc */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			/*'AuthConsumer' => [ // @deprecated Not used since Basic Auth was implemented to replace API authentication
				'class' => 'api\modules\v1\components\security\ApiConsumerSecurity',
			],*/
			'authenticator' => [
				'class' => 'yii\filters\auth\HttpBasicAuth',
				'auth' => [$this, 'auth'],
			]
		]);
	}

	/**
	 * Authenticates user.
	 *
	 * This function is used by HttpBasicAuth yii authenticator.
	 * Finds user by username and password (db fields: auth_secret and auth_token)
	 *
	 * @param string $username
	 * @param string $password
	 * @return static|null
	 */
	public function auth($username, $password)
	{
		if (empty($username) || empty($password))
			return null;

		// Find user
		if (($this->apiConsumer = ApiConsumerEx::findByKeySecret($username, $password)) === null) {
			return null;
		}

		// Check if user is active
		if (!$this->apiConsumer->isActive()) {
			return null;
		}

		/**
		 * Set user identity without touching session or cookie.
		 * (this is preferred use in stateless RESTful API implementation)
		 */
		Yii::$app->user->setIdentity($this->apiConsumer);

		/**
		 * User successfully authenticated.
		 *
		 * @see yii\web\User
		 *
		 * Yii::$app->user->identity to access currently authenticated user.
		 * Yii::$app->user->identity->customer to access currently authenticated customer if any.
		 *
		 */

		// Log user activity
		$this->apiConsumer->updateLastActivity()->save();

		return $this->apiConsumer;
	}

	/**
	 * Successful response
	 *
	 * This function sets the response status code 200
	 * and returns the response.
	 *
	 * @param $response
	 * @param $code
	 *
	 * @return array
	 */
	public function success($response, $code = 200)
	{
		$this->response->setStatusCode($code);

		return $response;
	}

	/**
	 * Error response
	 *
	 * @param int $code       HTTP code
	 * @param string $message Error message
	 *
	 * @return array
	 */
	public function errorMessage($code, $message)
	{
		$this->response->setStatusCode($code);

		return ['message' => $message];
	}

	/**
	 * Define the response status code to 422 (unprocessable entity) and return the errors.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function unprocessableError($data)
	{
		$this->response->setStatusCode(422);

		return ['errors' => $data];
	}
}