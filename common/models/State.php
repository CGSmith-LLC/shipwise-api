<?php

namespace common\models;

use common\models\base\BaseState;
use yii\helpers\ArrayHelper;

/**
 * Class State
 *
 * @package common\models
 */
class State extends BaseState
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