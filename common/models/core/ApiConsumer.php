<?php

namespace common\models\core;

use common\models\core\base\BaseApiConsumer;

/**
 * Class ApiConsumer
 *
 * @package common\models\core
 */
class ApiConsumer extends BaseApiConsumer
{
	const STATUS_ACTIVE   = 1;
	const STATUS_INACTIVE = 0;

	/**
	 * Get Customer
	 *
	 * ApiConsumer relation to Customer
	 *
	 * @todo Pending customer model generation
	 *
	 * @return \yii\db\ActiveQuery
	 */
	/*public function getCustomer()
	{
		return $this->hasOne('common\models\customer\Customer', ['id' => 'customer_id']);
	}*/

	/**
	 * Find Api Consumer by auth token
	 *
	 * @param string $token
	 *
	 * @return null|ApiConsumer
	 */
	protected static function findByToken($token)
	{
		return static::findOne(['auth_token' => $token]);
	}

	/**
	 * Find Api Consumer by key and secret
	 *
	 * @param string $key
	 * @param string $secret
	 *
	 * @return null|ApiConsumer
	 */
	protected static function findByKeySecret($key, $secret)
	{
		return static::findOne(['key' => $key, 'secret' => $secret]);
	}

	/**
	 * Is Api Consumer active
	 *
	 * @return bool
	 */
	protected function isActive()
	{
		return (bool)($this->status == self::STATUS_ACTIVE);
	}

	/**
	 * Reset Token
	 */
	protected function resetToken()
	{
		$this->auth_token         = null;
		$this->token_generated_on = null;
	}


}
