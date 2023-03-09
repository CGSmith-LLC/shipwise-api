<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\PaginatedControllerEx;
use api\modules\v1\models\alias\AliasChildrenEx;
use api\modules\v1\models\alias\AliasEx;
use api\modules\v1\models\forms\AliasForm;
use yii\data\ActiveDataProvider;

/**
 *
 * @package api\modules\v1\controllers
 */
class AliasController extends PaginatedControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'index'  => ['GET'],
            'delete'  => ['DELETE'],
        ];
    }

    /**
     * @SWG\Get(
     *     path = "/aliases",
     *     tags = { "Alias" },
     *     summary = "Fetch all Aliases",
     *     description = "Fetch all Aliases. Aliases are item mappings for customers",
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
     *              @SWG\Items( ref = "#/definitions/Alias" )
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
                'query' => AliasEx::find()->forCustomer($this->apiConsumer->customer->id)->active(),
                'pagination' => $this->pagination,
            ]
        );

        return $this->success($provider);
    }


    /**
     * @SWG\Post(
     *     path = "/aliases",
     *     tags = { "Alias" },
     *     summary = "Creates a new alias",
     *     description = "Creates a new alias and returns it back if created successfully. Alias is ACTIVE immediately.",
     *
     *     @SWG\Parameter(
     *                name = "AliasForm",
     *                in = "body",
     *                required = true,
     *                @SWG\Schema(ref = "#/definitions/AliasForm"),
     *     ),
     *
     *     @SWG\Response(
     *          response = 201,
     *          description = "Alias created successfully",
     *          @SWG\Schema(
     *              ref = "#/definitions/Alias"
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
     *     ),
     *
     *     security = {{ "basicAuth": {} }}
     * )
     */
    public function actionCreate()
    {
        $model = new AliasForm();
        $model->setAttributes($this->request->getBodyParams());
        $model->customer_id = $this->apiConsumer->customer_id;
        if ($model->validate()) {
            $id = $model->save();
            $model = AliasEx::findOne($id);
        }
        return $this->success($model, 201);
    }


    /**
     * @SWG\Delete(
     *     path = "/aliases/{id}",
     *     tags = { "Alias" },
     *     summary = "Delete an alias",
     *     description = "Deletes a specific alias",
     *
     *     @SWG\Parameter( name = "id", in = "path", type = "integer", required = true ),
     *
     *     @SWG\Response(
     *          response = 204,
     *          description = "Alias deleted successfully",
     *     ),
     *
     *     @SWG\Response(
     *          response = 400,
     *          description = "Error while deleting alias",
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
     *          description = "Alias not found",
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
        if (($order = AliasEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Alias not found');
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {

            // Delete order and items
            if ($order->delete()) {
                AliasChildrenEx::deleteAll(['alias_id' => (int)$id]);
            } else {
                $transaction->rollBack();

                return $this->errorMessage(400, 'Could not delete alias');
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not delete alias');
        } catch (\Throwable $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not delete alias');
        }

        $this->response->setStatusCode(204);
    }
}