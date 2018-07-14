<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\security\ApiConsumerSecurity;

use api\modules\v1\models\forms\AuthenticationForm;
use api\modules\v1\models\AuthenticationResponse;
use api\modules\v1\models\core\ApiConsumerEx;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class AuthController
 *
 * @package api\modules\v1\controllers
 */
class AuthController extends Controller
{
	/** @var  yii\web\Request */
	protected $request;

	/** @var  \yii\web\Response */
	protected $response;

	/** @inheritdoc */
	public function init()
	{
		parent::init();

		$this->request  = Yii::$app->request;
		$this->response = Yii::$app->response;
	}

	/**
	 * Register all behaviors
	 *
	 * @return array
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'AuthConsumer' => [
				'class'  => 'api\modules\v1\components\security\ApiConsumerSecurity',
				'except' => ['login'], // behavior isn't executed for these actions
			],
		]);
	}

	/**
	 * Declare HTTP verbs allowed for each methods
	 *
	 * @return array
	 */
	protected function verbs()
	{
		return [
			'login'  => ['post'],
			'logout' => ['delete'],
		];
	}

	/**
	 * @SWG\Post(
	 *     path="/auth",
	 *     tags={"Authentication"},
	 *     summary="Authenticates a user into the API",
	 *
	 *     @SWG\Parameter(
	 *          in = "body",
	 *          name = "AuthenticationRequest",
	 *          description = "Authentication request form",
	 *          required = true,
	 *          @SWG\Schema( ref = "#/definitions/AuthenticationRequest" ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Authentication successful. Response contains API token.",
	 *          @SWG\Schema( ref = "#/definitions/AuthenticationResponse" ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 401,
	 *          description = "Authentication failed",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 403,
	 *          description = "User is inactive",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 422,
	 *          description = "Fields are missing or invalid",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorData" )
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 500,
	 *          description = "Unexpected error",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
	 * )
	 */
	public function actionLogin()
	{
		/*
		 * Authenticate user from the information provided in authentication request form
		 */
		$userAuth             = new AuthenticationForm();
		$userAuth->attributes = $this->request->post();


		//  validate fields
		if (!$userAuth->validate()) {
			$this->response->setStatusCode(422);
			return ['errors' => $userAuth->getErrors()];
		}

		/*
		 *  Depending on authentication result, either throw an error or return authentication token
		 */
		switch ($userAuth->authenticate()) {

			case AuthenticationForm::ERR_AUTH_FAILURE :
				throw new UnauthorizedHttpException('Authentication failed.');

			case AuthenticationForm::ERR_INACTIVE :
				throw new ForbiddenHttpException('User is inactive.');

		}

		// Return response containing authentication token
		return new AuthenticationResponse($userAuth->getApiConsumer()->getAuthKey());
	}

	/**
	 * @SWG\Delete(
	 *     path = "/auth",
	 *     tags = { "Authentication" },
	 *     summary = "Logout authenticated user",
	 *     description = "Allows an authenticated user to log out",
	 *
	 *     @SWG\Response( response = 204, description = "Logout successful", ),
	 *
	 *     @SWG\Response(
	 *          response = 401,
	 *          description = "Impossible to authenticate user",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
	 *
	 *     @SWG\Response(
	 *          response = 500,
	 *          description = "Unexpected error",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
	 * )
	 */
	public function actionLogout()
	{

		$user = ApiConsumerEx::findIdentityByAccessToken($this->token);

		$user->resetToken();
		$user->save();

		//  set status code to 204
		$this->response->setStatusCode(204);
	}
}