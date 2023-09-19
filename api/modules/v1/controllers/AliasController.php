<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\PaginatedControllerEx;
use api\modules\v1\models\alias\AliasChildrenEx;
use api\modules\v1\models\alias\AliasEx;
use api\modules\v1\models\forms\AliasChildForm;
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
            'create'  => ['POST'],
            'index'   => ['GET'],
            'delete'  => ['DELETE'],
            'purge'  => ['DELETE'],
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
                'query' => AliasEx::find()->forCustomer($this->apiConsumer->customer->id)->active(),
                'pagination' => $this->pagination,
            ]
        );

        return $this->success($provider);
    }


    /**
     * @SWG\Delete(
     *     path = "/aliases/purge",
     *     tags = { "Alias" },
     *     summary = "Delete all aliases",
     *     description = "Removes all aliased items. This only removes it from Shipwise. If there is a sync the aliases
     * will come back into the system",
     *
     *     @SWG\Response(
     *          response = 204,
     *          description = "Aliases deleted successfully",
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

        AliasEx::deleteAll(['customer_id' => $this->apiConsumer->customer->id]);

        return $this->success(null, 204);
    }


    /**
     * @SWG\Post(
     *     path = "/aliases",
     *     tags = { "Alias" },
     *     summary = "Create new Alias with Children",
     *     description = "Creates new Alias with associated children.",
     *
     *     @SWG\Parameter(
     *          name = "AliasForm", in = "body", required = true,
     *          @SWG\Schema( ref = "#/definitions/AliasForm" ),
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
     *          description = "Error while creating model",
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
     * Create new alias form
     *
     * @return array|string[]
     */
    public function actionCreate()
    {
        $aliasForm = new AliasForm();
        $aliasForm->setAttributes($this->request->getBodyParams());
        $aliasForm->customer_id = $this->apiConsumer->customer->id; // Set customer_id to called API consumer

        // Check to see if our form validated correctly
        if (!$aliasForm->validate()) {
            return $this->unprocessableError($aliasForm->getErrors());
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $alias = new AliasEx([
                'customer_id' => $aliasForm->customer_id,
                'sku' => $aliasForm->sku,
                'name' => $aliasForm->name,
            ]);
            if ($alias->save()) {
                /** @var AliasChildForm $child */
                foreach ($aliasForm->children as $child) {
                    $childForm = new AliasChildForm();
                    $childForm->setAttributes($child);
                    \Yii::debug($childForm);
                    if (!$childForm->validate()) {
                        return $this->unprocessableError($childForm->getErrors());
                    }

                    $aliasChild = new AliasChildrenEx([
                        'alias_id' => $alias->id,
                        'sku' => $childForm->sku,
                        'name' => $childForm->name,
                        'quantity' => $childForm->quantity,
                    ]);
                    $aliasChild->save();
                }

                $transaction->commit(); // Commit DB transaction
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->errorMessage(400, 'Could not save alias correctly');
        }

        // refresh data before returning for 200
        $alias->refresh();

        return $this->success($alias, 201);
    }


    /**
     * Delete order
     *
     * @param int $id Alias ID
     *
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        // Find the order to delete
        if (($alias = AliasEx::find()
                ->byId($id)
                ->forCustomer($this->apiConsumer->customer->id)
                ->one()
            ) === null) {
            return $this->errorMessage(404, 'Alias not found');
        }

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            // Delete alias and children
            if ($alias->delete()) {
                AliasChildrenEx::deleteAll(['alias_id' => (int)$id]);
            } else {
                $transaction->rollBack();

                return $this->errorMessage(400, 'Could not delete alias');
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not delete order');
        }

        $this->response->setStatusCode(204);
    }
}