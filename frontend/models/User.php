<?php

namespace frontend\models;

use Stripe\Customer;
use yii\helpers\ArrayHelper;

/**
 * Class User
 *
 * @package frontend\models
 *
 * @property Customer[] $customers
 * @property integer $customer_id
 */
class User extends \common\models\User
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

    /**
     * Returns list of customers as array [id=>name]
     *
     * @param string $keyField Field name to use as key
     * @param string $valueField Field name to use as value
     *
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerList($keyField = 'id', $valueField = 'name')
    {
        $data = $this->getCustomers()->orderBy([$valueField => SORT_ASC])->all();

        return ArrayHelper::map($data, $keyField, $valueField);
    }

    public function isDirectCustomer()
    {
        /**
         * Admins need to be able to see the Billings page without being a direct customer
         */
        if ($this->isAdmin) {
            return true;
        }

        $customer = \frontend\models\Customer::findOne($this->customer_id);
        return $customer ? $customer->direct : false;
    }


    /**
     * Get array of associated customers IDs
     *
     * @return array
     */
    public function getCustomerIds()
    {
        return ArrayHelper::getColumn(
            $this->hasMany('frontend\models\UserCustomer', ['user_id' => 'id'])
                ->select('customer_id')
                ->asArray()
                ->all(),
            'customer_id');
    }
}