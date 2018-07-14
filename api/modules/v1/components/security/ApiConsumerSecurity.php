<?php

namespace api\modules\v1\components\security;

use api\modules\v1\models\core\ApiConsumerEx;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;
use yii\web\UnauthorizedHttpException;
use yii\rest\Controller;
use Yii;

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
	 */
	public function beforeAction($event)
	{
		if (in_array($event->action->id, $this->except)) {
			return;
		}

		$request = Yii::$app->request;
		$headers = $request->headers;

		$this->token = $headers->get("X-API-TOKEN");

		echo 'X-API-TOKEN:' . $this->token;
		exit;

		if (is_null($this->token) || empty($this->token)) {
			throw new UnauthorizedHttpException(
				'X-API-TOKEN key must be provided with urls requiring an authenticated user.'
			);
		}

		// Attempt to find api consumer
		$this->apiConsumer = ApiConsumerEx::findIdentityByAccount($this->authAccountNumber);

		if (empty($this->apiConsumer)) {
			throw new UnauthorizedHttpException('Authentication failure. Empty or invalid credentials.');
		}

		// search for api client
		$this->apiUser = ApiUser::findIdentityByAccessToken($this->token);

		if (empty($this->apiUser)) {
			throw new UnauthorizedHttpException(Yii::t('api.error', 'API-TOKEN provided is invalid, please login again.'));
		}

		//  log user activity
		Yii::$app->user->login($this->apiUser);

		$this->apiUser->logActivity();

	}
}