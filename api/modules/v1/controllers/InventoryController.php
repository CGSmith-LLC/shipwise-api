<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\PaginatedControllerEx;
use api\modules\v1\models\forms\InventoryForm;
use api\modules\v1\models\order\InventoryEx;
use yii\data\ActiveDataProvider;

/**
 *
 * @package api\modules\v1\controllers
 */
class InventoryController extends PaginatedControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'create'  => ['POST'],
            'index'   => ['GET'],
            'delete'  => ['DELETE'],
        ];
    }

    /**
     * @SWG\Post(
     *     path = "/inventory",
     *     tags = { "Inventory" },
     *     summary = "Create new Inventory",
     *     description = "Creates new inventory",
     *
     *     @SWG\Parameter(
     *          name = "InventoryForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/InventoryForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 201,
     *          description = "Inventory created successfully",
     *          @SWG\Schema(
     *              ref = "#/definitions/Inventory"
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while creating Inventory",
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
     * Create new inventory form
     *
     * @return array|string[]
     */
    public function actionCreate()
    {
        $inventoryForm = new InventoryForm();
        $inventoryForm->setAttributes($this->request->getBodyParams());
        $inventoryForm->customer_id = $this->apiConsumer->customer->id; // Set customer_id to called API consumer

        // Check to see if our form validated correctly
        if (!$inventoryForm->validate()) {
            return $this->unprocessableError($inventoryForm->getErrors());
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $inventory = new InventoryEx([
                'customer_id' => $inventoryForm->customer_id,
                'sku' => $inventoryForm->sku,
                'name' => $inventoryForm->name,
                'available_quantity' => $inventoryForm->available_quantity,
            ]);
            if ($inventory->save()) {
                $transaction->commit(); // Commit DB transaction
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->errorMessage(400, 'Could not save inventory');
        }

        return $this->success($inventory, 201);
    }

    /**
     * @SWG\Get(
     *     path = "/inventory",
     *     tags = { "Inventory" },
     *     summary = "Fetch all inventory",
     *     description = "Fetch all inventoried items",
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
     *          description = "Successful operation. Response contains a list of inventoried items.",
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
     *              @SWG\Items( ref = "#/definitions/Inventory" )
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
     *            "basicAuth": {}
     *     }}
     * )
     */
    public function actionIndex()
    {
        // Get paginated results
        $provider = new ActiveDataProvider([
            'query'      => InventoryEx::find()->forCustomer($this->apiConsumer->customer->id)->orderBy(['sku' => SORT_ASC]),
            'pagination' => $this->pagination,
        ]);

        return $this->success($provider);
    }

    /**
     * @SWG\Delete(
     *     path = "/inventory/purge",
     *     tags = { "Inventory" },
     *     summary = "Delete all inventory",
     *     description = "Removes all inventoried items",
     *
     *     @SWG\Response(
     *          response = 204,
     *          description = "Inventory deleted successfully",
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
    public function actionPurge()
    {
        // Ensure we are an API consumer and not a super admin
        if (!$this->apiConsumer->isCustomer()) {
            return $this->unprocessableError(['You must be a customer to be able to delete']);
        }

        InventoryEx::deleteAll(['customer_id' => $this->apiConsumer->customer->id]);

        return $this->success(null, 204);
    }

}