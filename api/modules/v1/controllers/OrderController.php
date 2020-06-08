<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\core\AddressEx;
use api\modules\v1\models\forms\HistoryForm;
use api\modules\v1\models\forms\StatusForm;
use api\modules\v1\models\order\OrderHistoryEx;
use api\modules\v1\models\order\PackageEx;
use api\modules\v1\models\order\TrackingInfoEx;
use api\modules\v1\models\order\ItemEx;
use api\modules\v1\models\order\StatusEx;
use common\models\PackageItem;
use common\models\PackageItemLotInfo;
use simpleDI\LoadedTestWithDependencyInjectionCest;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\modules\v1\models\order\OrderEx;
use api\modules\v1\models\forms\OrderForm;

/**
 * Class OrderController
 *
 * @package api\modules\v1\controllers
 *
 * @property \api\modules\v1\components\parameters\Pagination $pagination
 * @property \common\models\ApiConsumer                       $apiConsumer
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
            'packages'     => ['GET'],
            'findbystatus' => ['GET'],
            'status'       => ['POST'],
            'find'         => ['GET'],
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
     *                      type = "integer",
     *                ),
     *            },
     *          @SWG\Schema(
     *              type = "array",
     *              @SWG\Items( ref = "#/definitions/Order" )
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {},
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
         *
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 422,
     *          description = "Fields are missing or invalid",
     *          @SWG\Schema( ref = "#/definitions/ErrorData" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
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
            $address->company  = $orderForm->shipTo->company;
            $address->email    = $orderForm->shipTo->email;
            $address->address1 = $orderForm->shipTo->address1;
            $address->address2 = $orderForm->shipTo->address2;
            $address->city     = $orderForm->shipTo->city;
            $address->state_id = $orderForm->shipTo->stateId;
            $address->zip      = $orderForm->shipTo->zip;
            $address->phone    = $orderForm->shipTo->phone;
            $address->notes    = $orderForm->shipTo->notes;
            $address->save();

            // Create Order
            $order                      = new OrderEx();
            $order->customer_id         = $this->apiConsumer->customer->id;
            $order->notes               = $orderForm->notes;
            $order->uuid                = $orderForm->uuid;
            $order->po_number           = $orderForm->poNumber;
            $order->origin              = $orderForm->origin;
            $order->carrier_id          = $orderForm->carrier_id;
            $order->service_id          = $orderForm->service_id;
            $order->order_reference     = $orderForm->orderReference;
            $order->customer_reference  = $orderForm->customerReference;
            $order->requested_ship_date = $orderForm->requestedShipDate;
            $order->status_id           = isset($orderForm->status) ? $orderForm->status : null;
            $order->address_id          = $address->id;

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
                $item->uuid     = $formItem->uuid;
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
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
     * @SWG\Post(
     *     path = "/orders/{id}/history",
     *     tags = { "Orders" },
     *     summary = "Create history and associate with an order",
     *     description = "Creates new history",
     *
     *     @SWG\Parameter(
     *          name = "HistoryForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/HistoryForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 201,
     *          description = "History created successfully",
     *          @SWG\Schema(
     *              ref = "#/definitions/OrderHistory"
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while creating order",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 422,
     *          description = "Fields are missing or invalid",
     *          @SWG\Schema( ref = "#/definitions/ErrorData" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
     *     }}
     * )
     */
    public function actionHistory($id)
    {
        // Find the order to update
        if (($order = OrderEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Order not found');
        }

        // Build the Order Form with the attributes sent in request
        $historyForm = new HistoryForm();
        $historyForm->setAttributes($this->request->getBodyParams());

        if (!$historyForm->validate()) {
            return $this->unprocessableError($historyForm->getErrorsAll());
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $history = new OrderHistoryEx();
            $history->order_id = $order->id;
            $history->status_id = $order->status_id;
            $history->comment = $historyForm->comment;

            // Save Order model
            if (!$history->save()) {
                $transaction->rollBack();

                return $this->errorMessage(400, 'Could not save history');
            }

            // Commit DB transaction
            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::debug($history);

            return $this->errorMessage(400, 'Could not save history');
        } catch (\Throwable $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save history');
        }

        $history->refresh();

        return $this->success($history, 201);
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 422,
     *          description = "Fields are missing or invalid",
     *          @SWG\Schema( ref = "#/definitions/ErrorData" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
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

                if (isset($orderForm->shipTo->address2) && !empty($orderForm->shipTo->address2)) {
                    $address->address2 = $orderForm->shipTo->address2;
                }

                $address->city     = $orderForm->shipTo->city;
                $address->state_id = $orderForm->shipTo->stateId;
                $address->zip      = $orderForm->shipTo->zip;

                if (isset($orderForm->shipTo->phone) && !empty($orderForm->shipTo->phone)) {
                    $address->phone = $orderForm->shipTo->phone;
                }

                if (isset($orderForm->shipTo->notes) && !empty($orderForm->shipTo->notes)) {
                    $address->notes = $orderForm->shipTo->notes;
                }

                $address->save();
            }

            /**
             * Update carrier information
             */
            if (!empty($orderForm->carrier_id)) {
                $order->carrier_id = $orderForm->carrier_id;
            }
            if (!empty($orderForm->service_id)) {
                $order->service_id = $orderForm->service_id;
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
            $order->po_number          = $orderForm->poNumber;
            $order->customer_reference = $orderForm->customerReference;
            $order->status_id          = $orderForm->status;

            // Validate the order model itself
            if (!$order->validate()) {
                // if you get here then you should check if you have enough OrderForm validation rules
                $transaction->rollBack();

                return $this->unprocessableError($order->getErrors());
            }

            // Packages

            if (!empty($orderForm->packages)) {
                PackageEx::deleteAll(['order_id' => $order->id]);
                foreach ($orderForm->packages as $formPackage) {
                    $package = new PackageEx();
                    $package->setAttribute('length', $formPackage['length']);
                    $package->setAttribute('width', $formPackage['width']);
                    $package->setAttribute('height', $formPackage['height']);
                    $package->setAttribute('tracking', $formPackage['tracking']);
                    $package->setAttribute('order_id', $order->id);
                    $package->save();
                    foreach ($formPackage['package_items'] as $package_item) {
                        $packageItem = new PackageItem();
                        $packageItem->setAttribute('quantity', $package_item['quantity']);
                        $packageItem->setAttribute('sku', $package_item['sku']);
                        $packageItem->setAttribute('name', $package_item['name']);
                        $packageItem->setAttribute('package_id', $package->id);
                        $packageItem->setAttribute('order_id', $order->id);
                        $packageItem->save();
                        if (isset($package_item['lot_info'])) {
                            foreach ($package_item['lot_info'] as $lot_info) {
                                $lotInfo = new PackageItemLotInfo();
                                $lotInfo->setAttribute('quantity', $lot_info['quantity']);
                                $lotInfo->setAttribute('lot_number', $lot_info['lot_number']);
                                $lotInfo->setAttribute('serial_number', $lot_info['serial_number']);
                                $lotInfo->setAttribute('package_items_id', $packageItem->id);
                                $lotInfo->save();
                            }
                        }
                    }
                }
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 422,
     *          description = "Fields are missing or invalid",
     *          @SWG\Schema( ref = "#/definitions/ErrorData" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
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
     *              @SWG\Items( ref = "#/definitions/Item" )
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
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
     * Get packages of a specific order
     *
     * @param int $id Order ID
     *
     * @return array|\api\modules\v1\models\order\PackageEx
     */
    public function actionPackages($id)
    {
        if (($order = OrderEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->with('packages.items')
                ->asArray()
                ->one()
        ) === null) {
            return $this->errorMessage(404, 'Order not found');
        }

        return $this->success($order['packages']);
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
     *                enum = {1,7,8,9,10,11},
     *                description = "Status value that need to be considered for filter
    1 - Shipped
    7 - Cancelled
    8 - Pending Fulfillment
    9 - Open
    10 - WMS Error
    11 - Completed"
     *            ),
     *     @SWG\Parameter(
     *                name = "page",
     *                in = "query",
     *                type = "integer",
     *                description = "The zero-based current page number",
     *                default = 0
     *            ),
     *     @SWG\Parameter(
     *                name = "createdDate",
     *                in = "query",
     *                type = "string",
     *                description = "Search for orders after created date",
     *                default = 0
     *            ),
     *     @SWG\Parameter(
     *                name = "updatedDate",
     *                in = "query",
     *                type = "string",
     *                description = "Search for orders after updated date",
     *                default = 0
     *            ),
     *     @SWG\Parameter(
     *                name = "requestedShipDate",
     *                in = "query",
     *                type = "string",
     *                description = "Search for orders on requested ship date",
     *                default = 0
     *            ),
     *     @SWG\Parameter(
     *                name = "origin",
     *                in = "query",
     *                type = "string",
     *                description = "Search for with a specific orign",
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
     *              @SWG\Items( ref = "#/definitions/Order" )
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 401,
     *          description = "Impossible to authenticate user",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Bad request",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {},
     *     }}
     * )
     */

    /**
     * Find orders by status
     *
     * @return \api\modules\v1\models\order\OrderEx[]
     * @throws \Exception
     */
    public function actionFindbystatus()
    {
        /**
         * If Api consumer is a customer, then retrieve his orders,
         * if not, we assume that the Api consumer is a superuser and return all orders
         *
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

        $updatedDate       = null;
        $createdDate       = null;
        $requestedShipDate = null;
        $origin            = null;
        if (null !== $this->request->get('updatedDate')) {
            $updatedDate = $this->request->get('updatedDate');
        }
        if (null !== $this->request->get('createdDate')) {
            $createdDate = $this->request->get('createdDate');
        }
        if (null !== $this->request->get('requestedShipDate')) {
            $requestedShipDate = $this->request->get('requestedShipDate');
        }
        if (null !== $this->request->get('origin')) {
            $origin = $this->request->get('origin');
        }

        $query = OrderEx::find()
            ->forCustomer($customerId)
            ->byStatus($statusId)
            ->with('packages.items');
        /**
         * Select by updated date and/or created date
         */
        if ($updatedDate !== null) {
            $query->afterUpdatedDate(new \DateTime($updatedDate));
        }

        if ($createdDate !== null) {
            $query->afterCreatedDate(new \DateTime($createdDate));
        }

        if ($requestedShipDate !== null) {
            $query->onOrBeforeRequestedDate($requestedShipDate);
        }

        if ($origin !== null) {
            $query->withOrigin($origin);
        }

        // Get paginated results
        $provider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => $this->pagination,
        ]);

        return $this->success($provider);
    }

    /**
     * @SWG\Post(
     *     path = "/orders/{id}/status",
     *     tags = { "Orders" },
     *     summary = "Updates specific order with status ID",
     *     description = "Update order with status ID that is specified",
     *
     *     @SWG\Parameter( name = "status", in = "path", type = "integer", required = true, description = "Status ID" ),
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
     *     }}
     * )
     */

    /**
     * Set status based on POST to the order. Easier then sending a PUT request
     *
     * @param int $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionStatus(int $id)
    {
        // Build the Status Form with the attributes sent in request
        $statusForm           = new StatusForm();
        $statusForm->setAttributes($this->request->getBodyParams());

        // Validate, return errors if any
        if (!$statusForm->validate()) {
            return $this->unprocessableError($statusForm->getErrors());
        }

        if (($order = OrderEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Order not found');
        }else {
            $order->status_id = $statusForm->status;
            $order->save();
        }

        return $this->success($order);
    }

    /**
     * @SWG\Get(
     *     path = "/orders/find",
     *     tags = { "Orders" },
     *     summary = "Find specific order by criteria such as customer reference",
     *     description = "Find specific order by criteria such as customer reference",
     *
     *     @SWG\Parameter(
     *          name = "customer-reference",
     *          in = "query",
     *          type = "string",
     *          description = "Customer reference (order identifier in customer database)"
     *      ),
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
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     @SWG\Response(
     *          response = 403,
     *          description = "User is inactive",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 404,
     *          description = "Order not found",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *     ),
     *
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       ),
     *
     *     security = {{
     *            "basicAuth": {}
     *     }}
     * )
     */

    /**
     * Find order by criteria
     *
     * such as customer reference number (from their database).
     * Params are passed in request GET array.
     *
     * @return array|\api\modules\v1\models\order\OrderEx
     */
    public function actionFind()
    {
        $customerReference = $this->request->get('customer-reference', null);

        if (($order = OrderEx::find()
                ->byCustomerReference($customerReference)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Order not found');
        }

        return $this->success($order);
    }
}