<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\core\AddressEx;
use api\modules\v1\models\order\TrackingInfoEx;
use api\modules\v1\models\order\ItemEx;
use api\modules\v1\models\order\StatusEx;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
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
			'index'        => ['GET'],
			'create'       => ['POST'],
			'update'       => ['PUT'],
			'view'         => ['GET'],
			'delete'       => ['DELETE'],
			'items'        => ['GET'],
			'findbystatus' => ['GET'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return ArrayHelper::merge(parent::behaviors(), [
			'Pagination' => [
				'class' => 'api\modules\v1\components\parameters\Pagination',
			],
			'Search'     => 'api\modules\v1\components\parameters\Search',
			'Limit'      => 'api\modules\v1\components\parameters\Limit',
		]);
	}

	/**
	 * @SWG\Get(
	 *     path = "/orders",
	 *     tags = { "Orders" },
	 *     summary = "Fetch all orders",
	 *     description = "Fetch all orders for authenticated user",
	 *     @SWG\Parameter(
	 *                name = "page",
	 *                in = "query",
	 *                type = "integer",
	 *                description = "The zero-based current page number",
	 *                default = 0
	 *            ),
	 *     @SWG\Parameter(
	 *                name = "per-page",
	 *                in = "query",
	 *                type = "integer",
	 *                description = "The number of items per page",
	 *                default = 10
	 *            ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains a list of orders.",
	 *          headers = {
	 *              @SWG\Header(
	 *                    header = "X-Pagination-Total-Count",
	 *                    description = "The total number of resources",
	 *                    type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Page-Count",
	 *                      description = "The number of pages",
	 *                      type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Current-Page",
	 *                      description = "The current page (1-based)",
	 *                      type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Per-Page",
	 *                      description = "The number of resources in each page",
	 *                    type = "integer",
	 *                ),
	 *            },
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

	/**
	 * Get all orders
	 *
	 * @return \api\modules\v1\models\order\OrderEx[]
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

		// Get paginated results
		$provider = new ActiveDataProvider([
			'query'      => OrderEx::find()->forCustomer($customerId),
			'pagination' => $this->pagination,
		]);

		return $this->success($provider);
	}

	/**
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

	/**
	 * Create new order
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\db\Exception
	 */
	public function actionCreate()
	{
		// Build the Order Form with the attributes sent in request
		$orderForm           = new OrderForm();
		$orderForm->scenario = OrderForm::SCENARIO_DEFAULT;
		$orderForm->setAttributes($this->request->getBodyParams());

		// Validate OrderForm and its related models, return errors if any
		if (!$orderForm->validateAll()) {
			return $this->unprocessableError($orderForm->getErrorsAll());
		}

		// Begin DB transaction
		$transaction = \Yii::$app->db->beginTransaction();

		try {

		    // @Todo - lookup address and set for ID if it matches.

			/**
			 * Create Address.
			 * At this stage the required shipTo object should be fully validated.
			 */
			$address           = new AddressEx();
			$address->name     = $orderForm->shipTo->name;
			$address->address1 = $orderForm->shipTo->address1;
			$address->address2 = $orderForm->shipTo->address2;
			$address->city     = $orderForm->shipTo->city;
			$address->state_id = $orderForm->shipTo->stateId;
			$address->zip      = $orderForm->shipTo->zip;
			$address->phone    = $orderForm->shipTo->phone;
			$address->notes    = $orderForm->shipTo->notes;
			$address->save();

			// Create Order
			$order                     = new OrderEx();
			$order->customer_id        = $this->apiConsumer->customer->id;
			$order->uuid               = $orderForm->uuid;
			$order->order_reference    = $orderForm->orderReference;
			$order->customer_reference = $orderForm->customerReference;
			$order->status_id          = isset($orderForm->status) ? $orderForm->status : null;
			$order->address_id         = $address->id;

			// Validate the order model itself
			if (!$order->validate()) {
				// if you get here then you should check if you have enough OrderForm validation rules
				$transaction->rollBack();
				return $this->unprocessableError($order->getErrors());
			}

			// Create TrackingInfo
			if (!empty($orderForm->tracking)) {
				/**
				 * This is in preparation of the future transition of
				 * tracking details into tracking_info DB table:
				 *
				 * Until the transition not happened, we save tracking number into orders.tracking field.
				 * Once the transition happens, save the full tracking object into tracking_info DB table.
				 * See below.
				 *
				 */
				// The actual "before transition" behaviour:
				$order->tracking = $orderForm->tracking->trackingNumber;

				// The behaviour to implement after transition:
				/*
				$tracking             = new TrackingInfoEx();
				$tracking->service_id = $orderForm->tracking->serviceId;
				$tracking->tracking   = $orderForm->tracking->trackingNumber;
				if ($tracking->save()) {
					$order->tracking_id = $tracking;
				}
				*/
			}

			// Save Order model
			if (!$order->save()) {
				$transaction->rollBack();
				return $this->errorMessage(400, 'Could not save order');
			}

			/**
			 * Create Items.
			 * At this stage the required items array should be fully validated.
			 */
			foreach ($orderForm->items as $formItem) {
				$item           = new ItemEx();
				$item->order_id = $order->id;
				$item->sku      = $formItem->sku;
				$item->quantity = $formItem->quantity;
				$item->name     = $formItem->name;
				$item->save();
			}

			// Commit DB transaction
			$transaction->commit();

		} catch (\Exception $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not save order');
		} catch (\Throwable $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not save order');
		}

		$order->refresh();
		return $this->success($order, 201);
	}

	/**
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

	/**
	 * Get a specific order
	 *
	 * @param int $id Order ID
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 */
	public function actionView($id)
	{
		if (($order = OrderEx::find()
				->byId($id)
				->forCustomer($this->apiConsumer->customer->id)
				->one()
			) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		return $this->success($order);
	}

	/**
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

	/**
	 * Update order
	 *
	 * @param int $id Order ID
	 *
	 * @return array|\api\modules\v1\models\order\OrderEx
	 * @throws \yii\base\InvalidConfigException
	 * @throws \yii\db\Exception
	 */
	public function actionUpdate($id)
	{
		// Build the Order Form with the attributes sent in request
		$orderForm           = new OrderForm();
		$orderForm->scenario = OrderForm::SCENARIO_UPDATE;
		$orderForm->setAttributes($this->request->getBodyParams());

		// Validate OrderForm and its related models, return errors if any
		if (!$orderForm->validateAll()) {
			return $this->unprocessableError($orderForm->getErrorsAll());
		}

		// Find the order to update
		if (($order = OrderEx::find()
				->byId($id)
				->forCustomer($this->apiConsumer->customer->id)
				->one()
			) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		// Begin DB transaction
		$transaction = \Yii::$app->db->beginTransaction();

		try {

			/**
			 * Update Address.
			 * At this stage the required shipTo object should be fully validated.
			 */
			if (($address = AddressEx::findOne($order->address_id)) !== null) {
				$address->name     = $orderForm->shipTo->name;
				$address->address1 = $orderForm->shipTo->address1;

				if (isset($orderForm->shipTo->address2) && !empty($orderForm->shipTo->address2))
					$address->address2 = $orderForm->shipTo->address2;

				$address->city     = $orderForm->shipTo->city;
				$address->state_id = $orderForm->shipTo->stateId;
				$address->zip      = $orderForm->shipTo->zip;

				if (isset($orderForm->shipTo->phone) && !empty($orderForm->shipTo->phone))
					$address->phone = $orderForm->shipTo->phone;

				if (isset($orderForm->shipTo->notes) && !empty($orderForm->shipTo->notes))
					$address->notes = $orderForm->shipTo->notes;

				$address->save();
			}

			/**
			 * Update TrackingInfo.
			 * At this stage the required tracking object should be fully validated.
			 */
			if (!empty($orderForm->tracking)) {
				/**
				 * This is in preparation of the future transition of
				 * tracking details into tracking_info DB table:
				 *
				 * Until the transition not happened, we save tracking number into orders.tracking field.
				 * Once the transition happens, save the full tracking object into tracking_info DB table.
				 * See below.
				 *
				 */
				// The actual "before transition" behaviour:
				$order->tracking = $orderForm->tracking->trackingNumber;

				// The behaviour to implement after transition:
				/*
				$tracking             = new TrackingInfoEx();
				$tracking->service_id = $orderForm->tracking->serviceId;
				$tracking->tracking   = $orderForm->tracking->trackingNumber;
				if ($tracking->save()) {
					$order->tracking_id = $tracking;
				}
				*/
			}

			/**
			 * Update Items.
			 * At this stage the required items array should be fully validated.
			 */
			ItemEx::deleteAll(['order_id' => $order->id]);
			foreach ($orderForm->items as $formItem) {
				$item           = new ItemEx();
				$item->order_id = $order->id;
				$item->sku      = $formItem->sku;
				$item->quantity = $formItem->quantity;
				$item->name     = $formItem->name;
				$item->save();
			}

			/**
			 * Update order.
			 */
			$order->order_reference    = $orderForm->orderReference;
			$order->customer_reference = $orderForm->customerReference;
			$order->status_id          = $orderForm->status;

			// Validate the order model itself
			if (!$order->validate()) {
				// if you get here then you should check if you have enough OrderForm validation rules
				$transaction->rollBack();
				return $this->unprocessableError($order->getErrors());
			}

			// Save Order model
			if (!$order->save()) {
				$transaction->rollBack();
				return $this->errorMessage(400, 'Could not save order');
			}

			// Commit DB transaction
			$transaction->commit();

		} catch (\Exception $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not save order');
		} catch (\Throwable $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not save order');
		}

		$order->refresh();
		return $this->success($order);
	}

	/**
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

	/**
	 * Delete order
	 *
	 * @param int $id Order ID
	 *
	 * @return array
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function actionDelete($id)
	{
		// Find the order to delete
		if (($order = OrderEx::find()
				->byId($id)
				->forCustomer($this->apiConsumer->customer->id)
				->one()
			) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		// Begin DB transaction
		$transaction = \Yii::$app->db->beginTransaction();

		try {

			// Delete order and items
			if ($order->delete()) {
				ItemEx::deleteAll(['order_id' => (int)$id]);
			} else {
				$transaction->rollBack();
				return $this->errorMessage(400, 'Could not delete order');
			}

			$transaction->commit();

		} catch (\Exception $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not delete order');
		} catch (\Throwable $e) {
			$transaction->rollBack();
			return $this->errorMessage(400, 'Could not delete order');
		}

		$this->response->setStatusCode(204);
	}

	/**
	 * @SWG\Get(
	 *     path = "/orders/{id}/items",
	 *     tags = { "Orders" },
	 *     summary = "Fetch order items",
	 *     description = "Get all items associated with order ID",
	 *
	 *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true, description = "Order ID" ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains a list of order items.",
	 *          @SWG\Schema(
	 *              type = "array",
	 *     			@SWG\Items( ref = "#/definitions/Item" )
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

	/**
	 * Get items of a specific order
	 *
	 * @param int $id Order ID
	 *
	 * @return array|\api\modules\v1\models\order\ItemEx
	 */
	public function actionItems($id)
	{
		if (($order = OrderEx::find()
				->byId($id)
				->forCustomer($this->apiConsumer->customer->id)
				->one()
			) === null) {
			return $this->errorMessage(404, 'Order not found');
		}

		return $this->success($order->items);
	}

	/**
	 * @SWG\Get(
	 *     path = "/orders/findbystatus",
	 *     tags = { "Orders" },
	 *     summary = "Fetch orders by status",
	 *     description = "Fetch orders by status for authenticated user",
	 *     @SWG\Parameter(
	 *                name = "status",
	 *                in = "query",
	 *                type = "integer",
	 *                required = true,
	 *                enum = {1,9},
	 *                description = "Status value that need to be considered for filter
							1 - Shipped
							9 - Open"
	 *            ),
	 *     @SWG\Parameter(
	 *                name = "page",
	 *                in = "query",
	 *                type = "integer",
	 *                description = "The zero-based current page number",
	 *                default = 0
	 *            ),
	 *     @SWG\Parameter(
	 *                name = "per-page",
	 *                in = "query",
	 *                type = "integer",
	 *                description = "The number of items per page",
	 *                default = 10
	 *            ),
	 *
	 *     @SWG\Response(
	 *          response = 200,
	 *          description = "Successful operation. Response contains a list of orders.",
	 *          headers = {
	 *              @SWG\Header(
	 *                    header = "X-Pagination-Total-Count",
	 *                    description = "The total number of resources",
	 *                    type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Page-Count",
	 *                      description = "The number of pages",
	 *                      type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Current-Page",
	 *                      description = "The current page (1-based)",
	 *                      type = "integer",
	 *                ),
	 *              @SWG\Header(
	 *                      header = "X-Pagination-Per-Page",
	 *                      description = "The number of resources in each page",
	 *                    type = "integer",
	 *                ),
	 *            },
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
	 *          response = 400,
	 *          description = "Bad request",
	 *     		@SWG\Schema( ref = "#/definitions/ErrorMessage" )
	 *       ),
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

	/**
	 * Find orders by status
	 *
	 * @return \api\modules\v1\models\order\OrderEx[]
	 */
	public function actionFindbystatus()
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

		// Validate status parameter
		$statusId = (int)$this->request->get('status');
		if (!StatusEx::find()->where(['id' => $statusId])->exists()) {
			return $this->errorMessage(
				400,
				'Incorrect status value. Valid values are: ' .
				implode(StatusEx::getIdsAsArray(), ', '));
		}

		// Get paginated results
		$provider = new ActiveDataProvider([
			'query' => OrderEx::find()
				->forCustomer($customerId)
				->byStatus($statusId),
			'pagination' => $this->pagination,
		]);

		return $this->success($provider);
	}
}