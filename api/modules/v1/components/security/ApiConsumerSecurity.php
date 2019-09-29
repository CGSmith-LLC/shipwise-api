<?php

namespace api\modules\v1\components\security;

use api\modules\v1\models\core\ApiConsumerEx;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use yii\rest\Controller;
use Yii;

/**
 * Class ApiConsumerSecurity
 *
 * @deprecated Not used since Basic Auth was implemented to replace API authentication.
 *
 * @package api\modules\v1\components\security
 */
class ApiConsumerSecurity extends Behavior
{
	/**
	 * Except
	 *
	 * List of actions that should not execute the "beforeAction" event
	 *
	 * @var array
	 */
	public $except = [];

	/**
	 * API Consumer
	 *
	 * @var \api\modules\v1\models\core\ApiConsumerEx
	 */
	public $apiConsumer;

	/**
	 * X-API-TOKEN obtained during authentication
	 *
	 * @var string
	 */
	public $token;

	/**
	 * @inheritdoc
	 */
	public function events()
	{
		return ArrayHelper::merge(parent::events(), [
			Controller::EVENT_BEFORE_ACTION => 'beforeAction',
		]);
	}

	/**
	 * Before each action this method is called (except for those inside the $except),
	 * it will verify that the X-API-TOKEN is correctly sent and is valid.
	 *
	 * @param $event
	 *
	 * @throws \yii\web\UnauthorizedHttpException
	 * @throws \yii\web\ForbiddenHttpException
	 */
	public function beforeAction($event)
	{
		if (in_array($event->action->id, $this->except)) {
			return;
		}

		$request = Yii::$app->request;
		$headers = $request->headers;

		$this->token = $headers->get("X-API-TOKEN");

		if (is_null($this->token) || empty($this->token)) {
			throw new UnauthorizedHttpException(
				'X-API-TOKEN header must be provided with urls requiring an authenticated user.'
			);
		}

		// Attempt to find api consumer
		if (($this->apiConsumer = ApiConsumerEx::findIdentityByAccessToken($this->token)) === null) {
			throw new UnauthorizedHttpException(
				'X-API-TOKEN provided is invalid, please authenticate again.'
			);
		}

		// Check for token expiration
		if ($this->apiConsumer->isTokenExpired()) {
			throw new UnauthorizedHttpException(
				'X-API-TOKEN provided is expired, please authenticate again.'
			);
		}

		// Check if user is active
		if (!$this->apiConsumer->isActive()) {
			throw new ForbiddenHttpException('User is inactive.');
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
	}
}