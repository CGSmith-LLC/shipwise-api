<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\order\OrderEx;
use api\modules\v1\models\forms\OrderForm;

/**
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 */
class OrderController extends ControllerEx
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
	 * Get all orders
	 * @todo Pagination
	 *
	 * @return \api\modules\v1\models\order\OrderEx[]
	 *
	 * @SWG\Get(
	 *     path = "/orders",
	 *     tags = { "Orders" },
	 *     summary = "Fetch all orders",
	 *     description = "Fetch all orders for authenticated user",
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains a list of orders.",
	 *          @SWG\Schema(
	 *              type = "array",
	 *     			@SWG\Items( ref = "#/definitions/Order" )
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
		/**
		 * If Api consumer is a customer, then retrieve his orders,
		 * if not, we assume that the Api consumer is a superuser and return all orders
		 * @see \api\modules\v1\components\security\ApiConsumerSecurity
		 * @see \common\models\ApiConsumer
		 */
		$customerId = $this->apiConsumer->isCustomer()
			? $this->apiConsumer->customer->id
			: null;

		// @todo Paginate results

		$orders = OrderEx::find()
			->forCustomer($customerId)
			->limit(100)
			->all();

		return $this->success($orders);
	}

	/**
	 * Create new order
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 * @throws \yii\base\InvalidConfigException
	 *
	 * @SWG\Post(
	 *     path = "/orders",
	 *     tags = { "Orders" },
	 *     summary = "Create new order",
	 *     description = "Creates new order",
	 *
	 *     @SWG\Parameter(
	 *          name = "OrderForm", in = "body", required = true,
	 *          @SWG\Schema( ref = "#/definitions/OrderForm" ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 201,
	 *          description = "Order created successfully",
	 *          @SWG\Schema(
	 *              ref = "#/definitions/Order"
	 *            ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 400,
	 *          description = "Error while creating order",
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
		// Build the Order Form with the attributes sent in request
		$form             = new OrderForm();
		$form->attributes = $this->request->getBodyParams();

		// Validate that all rules are respected
		if (!$form->validate()) {
			return $this->unprocessableError($form->getErrors());
		}

		// @todo Create Order with all related entities.

		// Create new order
		/*$order = new OrderEx(['name' => $form->name]);
		if ($order->validate() && $order->save()) {
			$order->refresh();
			return $this->success($order);
		} else {
			return $this->errorMessage(400, 'Could not save order');
		}*/
	}

	/**
	 * Get a specific order
	 *
	 * @param int $id Order ID
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 *
	 * @SWG\Get(
	 *     path = "/orders/{id}",
	 *     tags = { "Orders" },
	 *     summary = "Fetch a specific order",
	 *     description = "Fetch a specific order with full details by order ID",
	 *
	 *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains the order found.",
	 *          @SWG\Schema(
	 *              ref = "#/definitions/Order"
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
	 *          description = "Order not found",
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
		// @todo Authorization: Check order ownership

		if (($order = OrderEx::findOne((int)$id)) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		return $this->success($order);
	}

	/**
	 * Update order
	 *
	 * @param int $id Order ID
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 * @throws \yii\base\InvalidConfigException
	 *
	 * @SWG\Put(
	 *     path = "/orders/{id}",
	 *     tags = { "Orders" },
	 *     summary = "Update a specific order",
	 *     description = "Updates an existing order",
	 *
	 *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
	 *
	 *     @SWG\Parameter(
	 *          name = "OrderForm", in = "body", required = true,
	 *          @SWG\Schema( ref = "#/definitions/OrderForm" ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains updated order.",
	 *          @SWG\Schema(
	 *              ref = "#/definitions/Order"
	 *            ),
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 400,
	 *          description = "Error while updating order",
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
	 *          response = 404,
	 *          description = "Order not found",
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
	public function actionUpdate($id)
	{
		// Build the Order Form with the attributes sent in request
		$form             = new OrderForm();
		$form->attributes = $this->request->getBodyParams();

		// Validate that all rules are respected
		if (!$form->validate()) {
			return $this->unprocessableError($form->getErrors());
		}

		// Find the order to update
		if (($order = OrderEx::findOne((int)$id)) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		// @todo Authorization: Check order ownership
		// @todo Update order with all related entities.

		// Save order
		/*$order->setAttributes($form->attributes);
		if ($order->validate() && $order->save()) {
			$order->refresh();
			return $this->success($order);
		} else {
			return $this->errorMessage(400, 'Could not save order');
		}*/
	}

	/**
	 * Delete order
	 *
	 * @param int $id Order ID
	 *
	 * @return array
	 * @throws \Throwable
	 * @throws \yii\db\StaleObjectException
	 *
	 * @SWG\Delete(
	 *     path = "/orders/{id}",
	 *     tags = { "Orders" },
	 *     summary = "Delete an order",
	 *     description = "Deletes a specific order",
	 *
	 *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
	 *
	 *     @SWG\Response(
	 *          response = 204,
	 *          description = "Order deleted successfully",
	 *     ),
	 *
	 *     @SWG\Response(
	 *          response = 400,
	 *          description = "Error while deleting order",
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
	 *          response = 404,
	 *          description = "Order not found",
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
	public function actionDelete($id)
	{
		// Find the order to delete
		if (($order = OrderEx::findOne((int)$id)) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		// @todo Authorization: Check order ownership
		// @todo Delete order with all related entities which should be deleted

		// Delete order
		if (!$order->delete()) {
			return $this->errorMessage(400, 'Could not delete order');
		}

		$this->response->setStatusCode(204);
	}
}