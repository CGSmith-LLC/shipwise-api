<?php

namespace common\models;

use common\models\base\BaseApiConsumer;

/**
 * Class ApiConsumer
 *
 * @package common\models
 *
 * @property Customer $customer
 */
class ApiConsumer extends BaseApiConsumer
{

    const STATUS_ACTIVE      = 1;
    const STATUS_INACTIVE    = 0;
    const SUPERUSER_ACTIVE   = 1;
    const SUPERUSER_INACTIVE = 0;

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
        return static::findOne(['auth_key' => $key, 'auth_secret' => $secret]);
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

    protected function isSuperuser()
    {
        return (bool)($this->superuser == self::SUPERUSER_ACTIVE);
    }

    /**
     * Whether this Api consumer is a customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return (bool)isset($this->customer);
    }
}
