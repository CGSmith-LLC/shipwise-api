<?php

namespace frontend\models;

use dektrium\user\models\User as BaseUser;

/**
 * Class User
 *
 * @package frontend\models
 *
 * @property Customer[] $customers
 */
class User extends BaseUser
{
    /**
     * Get associated customers
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomers()
    {
        return $this->hasMany('frontend\models\Customer', ['id' => 'customer_id'])
            ->viaTable(UserCustomer::tableName(), ['user_id' => 'id']);
    }
}