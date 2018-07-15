<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;

// use api\modules\v1\models\customer\form\CustomerForm;

/**
 * Class Customer
 *
 * @package api\modules\v1\controllers
 */
class CustomerController extends ControllerEx
{
	/** @inheritdoc */
	protected function verbs()
	{
		return [
			'index'  => ['GET'],
			'create' => ['POST'],
			'update' => ['PUT'],
			'view'   => ['GET'],
			'delete' => ['DELETE'],
		];
	}

	/** @inheritdoc */
	public function actionIndex()
	{
		echo 'actionIndex ';
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionCreate()
	{
		echo 'actionCreate ';
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionView($id)
	{
		echo 'actionView id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionUpdate($id)
	{
		echo 'actionUpdate id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}

	/** @inheritdoc */
	public function actionDelete($id)
	{
		echo 'actionDelete id: ' . $id;
		print_r($this->apiConsumer->attributes);
		exit;
	}
}