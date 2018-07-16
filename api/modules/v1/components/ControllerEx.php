<?php

namespace api\modules\v1\components;

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

	/** @inheritdoc */
	public function init()
	{
		parent::init();

		$this->request  = Yii::$app->request;
		$this->response = Yii::$app->response;
	}

	/** @inheritdoc */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'AuthConsumer' => [
				'class' => 'api\modules\v1\components\security\ApiConsumerSecurity',
			],
		]);
	}

	/**
	 * Successful response
	 *
	 * This function sets the response status code 200
	 * and returns the response.
	 *
	 * @param $response
	 *
	 * @return array
	 */
	public function success($response)
	{
		$this->response->setStatusCode(200);

		return $response;
	}

	/**
	 * Error response
	 *
	 * @param int    $code    HTTP code
	 * @param string $message Error message
	 *
	 * @return array
	 */
	public function errorMessage($code, $message)
	{
		$this->response->setStatusCode($code);

		return ['message' => $message];
	}
}