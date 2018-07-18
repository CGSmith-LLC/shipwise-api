<?php

namespace common\models;

use common\models\base\BaseOrderHistory;

/**
 * Class OrderHistory
 *
 * @package common\models
 */
class OrderHistory extends BaseOrderHistory
{
	/**
	 * Get Status
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getStatus()
	{
		return $this->hasOne('common\models\Status', ['id' => 'status_id']);
	}
}