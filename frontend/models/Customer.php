<?php

namespace frontend\models;

use common\models\Customer as BaseCustomer;
use common\traits\CacheableListTrait;

/**
 * Class Customer
 *
 * @package frontend\models
 *
 * @property User[] $users
 */
class Customer extends BaseCustomer
{
    use CacheableListTrait;

    protected const LIST_CACHE_KEY = 'customers-list';

    public function init(): void
    {
        $this->setClearCacheEvents();
        parent::init();
    }

    /**
     * Get linked users
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getUsers()
    {
        return $this->hasMany('frontend\models\User', ['id' => 'user_id'])
            ->viaTable(UserCustomer::tableName(), ['customer_id' => 'id']);
    }

    /**
     * Get UserCustomer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCustomer()
    {
        return $this->hasMany('frontend\models\UserCustomer', ['customer_id' => 'id']);
    }

    /**
     * Whether the customer is linked to given user
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isLinkedToUser($userId)
    {
        return $this->getUserCustomer()->onCondition(['user_id' => (int)$userId])->exists();
    }
}
