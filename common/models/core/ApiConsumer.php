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
	 * Find Api Consumer by auth token
	 *
	 * @param string $token
	 *
	 * @return null|ApiConsumer
	 */
	public static function findByToken($token)
	{
		return static::findOne(['auth_token' => $token]);
	}
}
