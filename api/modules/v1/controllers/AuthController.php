<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\security\ApiConsumerSecurity;
use api\modules\v1\models\forms\AuthenticationForm;
use api\modules\v1\models\AuthenticationResponse;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use Yii;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;

/**
 * Class AuthController
 *
 * @deprecated Not used since Basic Auth was implemented to replace API authentication.
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

//	/**
//     * // @deprecated Not used since Basic Auth was implemented to replace API authentication
//	 * @ SWG\Post(
//	 *     path="/auth",
//	 *     tags={"Authentication"},
//	 *     summary="Authenticates a user into the API",
//	 *
//	 *     @ SWG\Parameter(
//	 *          in = "body",
//	 *          name = "AuthenticationForm",
//	 *          description = "Authentication request form",
//	 *          required = true,
//	 *          @ SWG\Schema( ref = "#/definitions/AuthenticationForm" ),
//	 *     ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 200,
//	 *          description = "Authentication successful. Response contains API token.",
//	 *          @S WG\Schema( ref = "#/definitions/AuthenticationResponse" ),
//	 *
//	 *     		@ SWG\Header(
//	 *            header = "X-Expires-After",
//	 *                description = "Date in UTC when token expires",
//	 *                type = "string",
//	 *       		@ SWG\Schema(
//	 *                type = "string",
//	 *                format = "date-time",
//	 *            ),
//	 *            ),
//	 *     ),
//	 *
//	 *	   @ SWG\Response(
//	 *          response = 400,
//	 *          description = "Bad request",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *     ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 401,
//	 *          description = "Authentication failed",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *     ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 403,
//	 *          description = "User is inactive",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *     ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 422,
//	 *          description = "Fields are missing or invalid",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorData" )
//	 *     ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 500,
//	 *          description = "Unexpected error",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *       ),
//	 * )
//	 */

	/**
	 * Action Login
	 *
	 * @return AuthenticationResponse|array
	 * @throws ForbiddenHttpException
	 * @throws UnauthorizedHttpException
	 * @throws \yii\base\Exception
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

		// Get authenticated user
		$apiConsumer = $userAuth->getApiConsumer();

		// Set response headers
		Yii::$app->response->headers->add(
			'X-Expires-After',
			$apiConsumer->getTokenExpiration()
		);

		// Return response containing authentication token
		return new AuthenticationResponse([
			'token' => $apiConsumer->getAuthKey(),
		]);
	}

//	/**
//     * // @deprecated Not used since Basic Auth was implemented to replace API authentication
//	 *  @ SWG\Delete(
//	 *     path = "/auth",
//	 *     tags = { "Authentication" },
//	 *     summary = "Logout authenticated user",
//	 *     description = "Allows an authenticated user to log out",
//	 *
//	 *     @ SWG\Response( response = 204, description = "Logout successful", ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 401,
//	 *          description = "Impossible to authenticate user",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *       ),
//	 *
//	 *     @ SWG\Response(
//	 *          response = 500,
//	 *          description = "Unexpected error",
//	 *     		@ SWG\Schema( ref = "#/definitions/ErrorMessage" )
//	 *       ),
//	 *     security = {{
//	 *            "apiTokenAuth": {},
//	 *     }}
//	 * )
//	 */

	/**
	 * Action Logout
	 *
	 * Logout current authenticated user
	 *
	 * @see ApiConsumerSecurity::$apiConsumer
	 */
	public function actionLogout()
	{
		$this->apiConsumer->resetToken()->save();
		Yii::$app->user->identity = null;

		$this->response->setStatusCode(204);
	}
}