<?php

namespace common\models;

use common\models\base\BaseService;
use yii\helpers\ArrayHelper;

/**
 * Class Service
 *
 * @package common\models
 */
class Service extends BaseService
{
	/**
	 * Get Carrier
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getCarrier()
	{
		return $this->hasOne('common\models\Carrier', ['id' => 'carrier_id']);
	}

	/**
	 * Get array of Service ids
	 *
	 * @return array
	 */
	public static function getIdsAsArray()
	{
		return ArrayHelper::getColumn(self::find()->select('id')->asArray()->all(), 'id');
	}
}