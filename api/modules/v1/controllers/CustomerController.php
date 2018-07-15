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
	/*protected function verbs()
	{
		return [
			"index"       => ["GET", "OPTIONS"],
			"acknowledge" => ["PUT", "OPTIONS"],
		];
	}*/

	/** @inheritdoc */
	public function actionIndex()
	{
		print_r($this->apiConsumer->attributes);
		exit;
	}
}