<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\PaginatedControllerEx;
use api\modules\v1\models\core\CountryEx;
use yii\data\ArrayDataProvider;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;

/**
 *
 * @package api\modules\v1\controllers
 */
class CountryController extends PaginatedControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'index'  => ['GET'],
        ];
    }


    /** @inheritdoc */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        $cors = [
            [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['https://app.csvbox.io', 'https://app.getshipwise.com'],
                    'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                ],
            ],
        ];
        return ArrayHelper::merge($behaviors, $cors);
    }

    /**
     * @SWG\Get(
     *     path = "/countries",
     *     tags = { "Countries" },
     *     summary = "Fetch all States/Provinces",
     *     description = "Fetch all States/Provinces",
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
     *          description = "Successful operation. Response contains a list of States/Provinces.",
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
     *              @SWG\Items( ref = "#/definitions/State" )
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
        $provider = new ArrayDataProvider(
            [
                'allModels' => CountryEx::getListForCsvBox(),
                'pagination' => false,
            ]
        );

        return $this->success($provider);
    }
}