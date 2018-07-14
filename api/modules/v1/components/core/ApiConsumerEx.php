<?php

namespace api\modules\v1\models\core;

use common\models\core\ApiConsumer;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;
use Yii;

/**
 * Class ApiConsumerEx
 *
 * @package api\modules\v1\models\core
 *
 */
class ApiConsumerEx extends ApiConsumer implements IdentityInterface
{
	/** @inheritdoc */
	public static function findIdentity($id)
	{
		return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
	}

	/** @inheritdoc */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
	}

	/** @inheritdoc */
	public function getId()
	{
		return $this->id;
	}

	/** @inheritdoc */
	public function getAuthKey()
	{
		return null;
	}

	/** @inheritdoc */
	public function validateAuthKey($authKey)
	{
		return null;
	}

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
	 * Find Identity by Auth Token
	 *
	 * @see \common\models\core\ApiConsumer
	 *
	 * @param string $authToken Auth token
	 *
	 * @return ApiConsumer
	 */
	public static function findIdentityByAccount($authToken)
	{
		return static::findByToken($authToken);
	}

	/**
	 * Generates authentication token
	 */
	public function generateAuthKey()
	{
		$this->auth_token = Yii::$app->security->generateRandomString();
	}
}
