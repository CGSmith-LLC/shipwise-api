<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\order\InventoryEX;
use common\models\base\BaseInventory;

/**
 *
 * @package api\modules\v1\controllers
 */
class InventoryController extends ControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'index'        => ['GET'],
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

    public function actionCreate()
    {
        $inventory = new InventoryEX();

        // Begin DB transaction
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            if ($inventory->load(\Yii::$app->request->post()) && $inventory->save()) {
                // Commit DB transaction
                $transaction->commit();
            }
        } catch (\Exception $e) {
            $transaction->rollBack();

            return $this->errorMessage(400, 'Could not save inventory');
        }

        return $this->success($inventory, 201);
    }

    public function actionIndex()
    {
        return $this->success(['hey'],200);
    }
}