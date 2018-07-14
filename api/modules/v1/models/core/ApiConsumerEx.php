<?php

namespace api\modules\v1\models\core;

use common\models\core\ApiConsumer;
use yii\web\IdentityInterface;
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
		return static::findOne(['id' => $id]);
	}

	/** @inheritdoc */
	public static function findIdentityByAccessToken($token, $type = null)
	{
		return static::findByToken($token);
	}

	/** @inheritdoc */
	public function getId()
	{
		return $this->id;
	}

	/** @inheritdoc */
	public function getAuthKey()
	{
		return $this->auth_token;
	}

	/** @inheritdoc */
	public function validateAuthKey($authKey)
	{
		return null;
	}

	/**
	 * Generates authentication token
	 */
	public function generateToken()
	{
		$this->auth_token = Yii::$app->security->generateRandomString();
		$this->token_generated_on = date('Y-m-d H:i:s');

		return $this;
	}

	/** @inheritdoc */
	public static function findByKeySecret($key, $secret)
	{
		return parent::findByKeySecret($key, $secret);
	}

	/** @inheritdoc */
	public function resetToken()
	{
		return parent::resetToken();
	}

	/** @inheritdoc */
	public function isActive()
	{
		return parent::isActive();
	}
}
