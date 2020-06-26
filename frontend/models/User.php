<?php

namespace frontend\models;

use dektrium\user\models\User as BaseUser;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use yii\base\Event;
use yii\helpers\ArrayHelper;

/**
 * Class User
 *
 * @package frontend\models
 *
 * @property Customer[] $customers
 */
class User extends BaseUser
{

    public function init()
    {
        $this->on(self::BEFORE_REGISTER, function () {
            $this->username = $this->email;
        });

        parent::init();
    }

    public function rules()
    {
        $rules = parent::rules();
        unset($rules['usernameRequired']);
        return $rules;
    }

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
     * @param string $keyField   Field name to use as key
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

    /**
     * Return the customer stripe id token from the first model
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerStripeId()
    {
        /** @var Customer $customer */
        $customer = $this->getCustomers()->one();
        return $customer->stripe_customer_id;
    }

    /**
     * Return the customer id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerId()
    {
        /** @var Customer $customer */
        $customer = $this->getCustomers()->one();
        return $customer->id;
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