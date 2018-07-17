<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\customer\CustomerEx;
use api\modules\v1\models\forms\CustomerForm;

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

	/**
	 * Create new customer
	 *
	 * @return array|\api\modules\v1\models\customer\CustomerEx
	 * @throws \yii\base\InvalidConfigException
	 *
	 * @SWG\Post(
	 *     path = "/customers",
	 *     tags = { "Customers" },
	 *     summary = "Create new customer",
	 *     description = "Creates new customer",
	 *
	 *     @SWG\Parameter(
	 *          name = "CustomerForm", in = "body", required = true,
	 *          @SWG\Schema( ref = "#/definitions/CustomerForm" ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 201,
	 *          description = "Customer created successfully",
	 *          @SWG\Schema(
	 *              ref = "#/definitions/Customer"
	 *            ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 400,
	 *          description = "Error while creating customer",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
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
	 *
	 *     security = {{
	 *            "apiTokenAuth": {}
	 *     }}
	 * )
	 */
	public function actionCreate()
	{
		$form             = new CustomerForm();
		$form->attributes = $this->request->getBodyParams();

		// Validate that all rules are respected
		if (!$form->validate()) {
			return $this->unprocessableError($form->getErrors());
		}

		// Create new customer
		$customer = new CustomerEx(['name' => $form->name]);
		if ($customer->validate() && $customer->save()) {
			$customer->refresh();
			return $this->success($customer);
		} else {
			return $this->errorMessage(400, 'Could not save customer');
		}
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