<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\PaginatedControllerEx;
use api\modules\v1\models\forms\SkuForm;
use api\modules\v1\models\sku\SkuEx;
use yii\data\ActiveDataProvider;

/**
 *
 * @package api\modules\v1\controllers
 */
class SkuController extends PaginatedControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'index'  => ['GET'],
            'delete' => ['DELETE'],
            'find'   => ['GET'],
        ];
    }

    /**
     * @SWG\Post(
     *     path = "/skus",
     *     tags = { "SKU" },
     *     summary = "Create SKUs",
     *     description = "SKUs are used to determine logic or autocomplete in the ShipWise App",
     *
     *     @SWG\Parameter(
     *          name = "SkuForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/SkuForm" ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 201,
     *          description = "SKU created successfully",
     *          @SWG\Schema(
     *              ref = "#/definitions/Sku"
     *            ),
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while creating SKU",
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
     * Create new sku form
     *
     * @return array|string[]
     */
    public function actionCreate()
    {
        $skuForm = new SkuForm();
        $skuForm->setAttributes($this->request->getBodyParams());
        $skuForm->customer_id = $this->apiConsumer->customer->id; // Set customer_id to called API consumer

        // Check to see if our form validated correctly
        if (!$skuForm->validate()) {
            return $this->unprocessableError($skuForm->getErrors());
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $sku = new SkuEx(
                [
                    'customer_id'  => $skuForm->customer_id,
                    'sku'          => $skuForm->sku,
                    'name'         => $skuForm->name,
                    'excluded'     => $skuForm->excluded,
                ]
            );
            if ($sku->save()) {
                $transaction->commit(); // Commit DB transaction
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->errorMessage(400, 'Could not save SKU');
        }

        return $this->success($sku, 201);
    }

    /**
     * @SWG\Get(
     *     path = "/skus",
     *     tags = { "SKU" },
     *     summary = "Fetch all SKUs",
     *     description = "Fetch all SKUs",
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
     *          description = "Successful operation. Response contains a list of SKUs.",
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
     *              @SWG\Items( ref = "#/definitions/Sku" )
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
        $provider = new ActiveDataProvider(
            [
                'query' => SkuEx::find()->forCustomer($this->apiConsumer->customer->id)->orderBy(['sku' => SORT_ASC]),
                'pagination' => $this->pagination,
            ]
        );

        return $this->success($provider);
    }

    /**
     * @SWG\Delete(
     *     path = "/skus/{id}",
     *     tags = { "SKU" },
     *     summary = "Delete SKU",
     *     description = "Removes a SKU",
     *
     *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
     *
     *     @SWG\Response(
     *          response = 204,
     *          description = "SKU deleted successfully",
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while deleting SKU",
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

    /**
     * Delete SKU
     *
     * @param int $id SKU ID
     *
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        // Find the order to delete
        if (($sku = SkuEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'SKU not found');
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $sku->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not delete SKU');
        }

        return $this->success(null, 204);
    }


    /**
     * @SWG\Get(
     *     path = "/skus/find",
     *     tags = { "SKU" },
     *     summary = "Find specific SKU",
     *     description = "Find specific SKU",
     *
     *     @SWG\Parameter(
     *          name = "sku",
     *          in = "query",
     *          type = "string",
     *          description = "SKU number"
     *      ),
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains the SKU found.",
     *          @SWG\Schema(
     *              ref = "#/definitions/Sku"
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
     * Find SKU by criteria
     * Params are passed in request GET array.
     *
     * @return array|\api\modules\v1\models\sku\SkuEx
     */
    public function actionFind(): array|\api\modules\v1\models\sku\SkuEx
    {
        $customerReference = $this->request->get('sku', null);

        if (($order = SkuEx::find()
                ->bySku($customerReference)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'SKU not found');
        }

        return $this->success($order);
    }
}