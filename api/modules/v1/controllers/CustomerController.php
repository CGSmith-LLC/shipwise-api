<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\customer\CustomerEx;

// use api\modules\v1\models\customer\form\CustomerForm;

/**
 * Class Customer
 *
 * @package api\modules\v1\controllers
 */
class CustomerController extends ControllerEx
{
	/** @inheritdoc */
	protected function verbs()
	{
		return [
			'index'  => ['GET'],
			'create' => ['POST'],
			'update' => ['PUT'],
			'view'   => ['GET'],
			'delete' => ['DELETE'],
		];
	}

	/**
	 * Fetch all customers
	 *
	 * @return \api\modules\v1\models\customer\CustomerEx[]
	 *
	 * @SWG\Get(
	 *     path = "/customers",
	 *     tags = { "Customers" },
	 *     summary = "Fetch all customers",
	 *     description = "Get all customers",
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains a list of customers.",
	 *          @SWG\Schema(
	 *              type = "array",
	 *     			@SWG\Items( ref = "#/definitions/Customer" )
	 *            ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 401,
	 *          description = "Impossible to authenticate user",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
	 *
	 *     @SWG\Response(
	 *          response = 403,
	 *          description = "User is inactive",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 500,
	 *          description = "Unexpected error",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
	 *
	 *     security = {{
	 *            "apiTokenAuth": {},
	 *     }}
	 * )
	 */
	public function actionIndex()
	{
		return CustomerEx::find()->all();
	}

	/** @inheritdoc */
	public function actionCreate()
	{
		echo 'actionCreate ';
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionView($id)
	{
		echo 'actionView id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionUpdate($id)
	{
		echo 'actionUpdate id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionDelete($id)
	{
		echo 'actionDelete id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}
}