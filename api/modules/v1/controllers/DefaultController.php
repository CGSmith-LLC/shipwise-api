<?php

namespace api\modules\v1\controllers;

use yii\helpers\Url;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `v1` module
 */
class DefaultController extends Controller
{

    /**
     * Renders the index view for the module
     */
    public function actionIndex()
    {
        throw new NotFoundHttpException('Unsupported action request.');
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            "doc"    => [
                "class"   => 'light\swagger\SwaggerAction',
                "restUrl" => [
                    [
                        'name' => "Ship Wise API V1",
                        'url'  => Url::to(["/v1/schema"], true),
                    ],
                    /* Example of adding doc for another version */
                    /*[
                        'name' => "Ship Wise API V2",
                        'url'  => Url::to(["/v2/schema"], true),
                    ],*/
                ],
            ],
            "schema" => [
                "class"   => 'light\swagger\SwaggerApiAction',
                "scanDir" => [
                    Yii::getAlias("@api/modules/v1/swagger"),
                    Yii::getAlias("@api/modules/v1/controllers"),
                    Yii::getAlias("@api/modules/v1/models"),
                ],
            ],
        ];
    }
}
