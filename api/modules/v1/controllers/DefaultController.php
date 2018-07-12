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
				"restUrl" => Url::to(["/v1/schema"], true),
			],
			"schema" => [
				"class"   => 'light\swagger\SwaggerApiAction',
				"scanDir" => [
					Yii::getAlias("@api/modules/v1/swagger"),
					Yii::getAlias("@api/modules/v1/controllers"),
					Yii::getAlias("@api/modules/v1/models"),
					Yii::getAlias("@common/models"),
				],
			],
		];
	}
}
