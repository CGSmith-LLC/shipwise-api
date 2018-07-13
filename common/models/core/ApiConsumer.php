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
