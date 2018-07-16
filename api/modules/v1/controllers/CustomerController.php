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
	 * Get all customers
	 * @todo Paginate results?
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
		return $this->success(
			CustomerEx::find()->all()
		);
	}

	/** @inheritdoc */
	public function actionCreate()
	{
		echo 'actionCreate ';
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/**
	 * Get a specific customer
	 *
	 * @param int $id Customer ID
	 *
	 * @return array|\api\modules\v1\models\customer\CustomerEx
	 *
	 * @SWG\Get(
	 *     path = "/customers/{id}",
	 *     tags = { "Customers" },
	 *     summary = "Fetch a specific customer",
	 *     description = "Fetch a specific customer with full details by ID",
	 *
	 *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains the customer found.",
	 *          @SWG\Schema(
	 *              ref = "#/definitions/Customer"
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
	 *          response = 404,
	 *          description = "Customer not found",
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
	 *            "apiTokenAuth": {}
	 *     }}
	 * )
	 */
	public function actionView($id)
	{
		if (($customer = CustomerEx::findOne((int)$id)) !== null) {
			return $this->success($customer);
		} else {
			return $this->errorMessage(404, 'Customer not found');
		}
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