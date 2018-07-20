<?php

namespace common\models;

use common\models\base\BaseStatus;
use yii\helpers\ArrayHelper;

/**
 * Class Status
 *
 * @package common\models
 */
class Status extends BaseStatus
{
	/**
	 * Get array of State ids
	 *
	 * @return array
	 */
	public static function getIdsAsArray()
	{
		return ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
	}
}