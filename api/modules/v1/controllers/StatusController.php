<?php

namespace api\modules\v1\controllers;

use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\order\StatusEx;

/**
 * Class StatusController
 * @package api\modules\v1\controllers
 */
class StatusController extends ControllerEx
{

    protected function verbs(): array
    {
        return [
            'index'  => ['GET'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $cors = [
            [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ControllerEx::$allowedCorsOriginDomains,
                    'Access-Control-Request-Method' => ControllerEx::$allowedCorsAccessControlRequestMethods,
                ],
            ],
        ];

        return ArrayHelper::merge($behaviors, $cors);
    }

    /**
     * @SWG\Get(
     *     path = "/statuses",
     *     tags = { "Order Statuses" },
     *     summary = "Fetch all Order Statuses",
     *     description = "Fetch all Order Statuses in list format.",
     *
     *     @SWG\Response(
     *          response = 200,
     *          description = "Successful operation. Response contains a list of Order Statuses.",
     *          @SWG\Schema(
     *              type = "array",
     *              @SWG\Items( ref = "#/definitions/OrderStatus" )
     *            ),
     *     ),
     *     @SWG\Response(
     *          response = 500,
     *          description = "Unexpected error",
     *          @SWG\Schema( ref = "#/definitions/ErrorMessage" )
     *       )
     * )
     */
    public function actionIndex(): array
    {
        return $this->success(StatusEx::getListForCsvBox());
    }
}
