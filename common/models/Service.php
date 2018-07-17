<?php

namespace common\models;

use common\models\base\BaseService;

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
}