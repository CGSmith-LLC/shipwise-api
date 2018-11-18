<?php

namespace api\modules\v1\components\parameters;

use yii\base\Behavior;
use yii\base\Controller;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Pagination
 *
 * This behavior class will get pagination parameters that are available in GET methods
 * and will make them available to any class having this behavior attached.
 *
 * @package api\modules\v1\components\parameters
 */
class Pagination extends Behavior
{
	const PAGE_NUMBER = 0;
	const PAGE_SIZE   = 10;

	CONST PAGE_SIZE_MIN = 0;
	const PAGE_SIZE_MAX = 1000;

	/** @var pagination */
	public $pagination;

	/** @var array List of actions that should not execute the "beforeAction" event */
	public $except = [];

	/**
	 * @inheritdoc
	 * @return array
	 */
	public function events()
	{
		return ArrayHelper::merge(parent::events(), [
			Controller::EVENT_BEFORE_ACTION => 'getPagination',
		]);
	}

	/**
	 * @param $event
	 */
	public function getPagination($event)
	{
		if (in_array($event->action->id, $this->except)) {
			return;
		}

		$request = Yii::$app->request;

		// Get the page number parameter
		$this->pagination = [
			'page'     => $request->get('page', self::PAGE_NUMBER),
			'pageSize' => $request->get('per-page', self::PAGE_SIZE),
		];

		// Verify that the page size doesn't ask for more result than the maximum
		if ($this->pagination['pageSize'] > self::PAGE_SIZE_MAX) {
			$this->pagination['pageSize'] = self::PAGE_SIZE_MAX;
		}

		// Verify that the page size doesn't ask for no result at all either
		if ($this->pagination['pageSize'] <= self::PAGE_SIZE_MIN) {
			$this->pagination['pageSize'] = self::PAGE_SIZE;
		}
	}
}