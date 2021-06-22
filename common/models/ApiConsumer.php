<?php

namespace common\models;

use common\models\base\BaseApiConsumer;
use Yii;

/**
 * Class ApiConsumer
 *
 * @package common\models
 *
 * @property Customer $customer
 */
class ApiConsumer extends BaseApiConsumer
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const SUPERUSER_ACTIVE = 1;
    const SUPERUSER_INACTIVE = 0;

    public $plainTextAuthSecret;

    /** @deprecated $auth_secret */
    public $auth_secret;


    /**
     * Get Customer
     *
     * ApiConsumer relation to Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

    /**
     * Get customer meta data
     *
     * ApiConsumer relation to Customer's Meta Data
     * @return \yii\db\ActiveQuery
     */
    public function getMeta()
    {
        return $this->hasMany('common\models\CustomerMeta', ['customer_id' => 'customer_id']);
    }

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
        if ($apiConsumer = static::find()->where(['auth_key' => $key])->one()) {
            /** @var ApiConsumer $apiConsumer */
            $authSecret = Yii::$app->getSecurity()->decryptByKey(base64_decode($apiConsumer->encrypted_secret), Yii::$app->params['encryptionKey']);
            if ($authSecret === $secret) {
                return $apiConsumer;
            }
        }
        return null;
    }

    /**
     * Is Api Consumer active
     *
     * @return bool
     */
    protected function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    protected function isSuperuser()
    {
        return $this->superuser == self::SUPERUSER_ACTIVE;
    }

    /**
     * Whether this Api consumer is a customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return isset($this->customer);
    }

    /**
     * Generate a random string for auth token and verify it does not exist
     *
     * @return ApiConsumer
     * @throws \yii\base\Exception
     *
     */
    public function generateAuthKey(): ApiConsumer
    {
        $this->auth_key = Yii::$app->getSecurity()->generateRandomString(6);

        while (ApiConsumer::find()->where(['auth_key' => $this->auth_key])->exists()) {
            $this->auth_key = Yii::$app->getSecurity()->generateRandomString(6);
        }

        return $this;
    }

    /**
     * Generate the encryption and API Secret
     *
     * @return $this
     * @throws \yii\base\Exception
     */
    public function generateAuthSecret(): ApiConsumer
    {
        // Generate random string for auth token
        $this->plainTextAuthSecret = Yii::$app->security->generateRandomString(64);
        $this->encrypted_secret = base64_encode(Yii::$app->getSecurity()->encryptByKey($this->plainTextAuthSecret, Yii::$app->params['encryptionKey']));

        return $this;
    }

    /**
     * Gets customer id
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        return Yii::$app->user->identity->customer_id;

    }
}
