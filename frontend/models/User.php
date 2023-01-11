<?php

namespace frontend\models;

use common\models\UserWarehouse;
use common\models\Warehouse;
use Da\User\Model\User as BaseUser;
use Da\User\Event\UserEvent;
use Stripe\Customer;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * Class User
 *
 * @package frontend\models
 *
 * @property Customer[] $customers
 * @property integer $customer_id
 * @property integer $type
 */
class User extends BaseUser
{
    public const TYPE_CUSTOMER = 0;
    public const TYPE_WAREHOUSE = 1;

    public function init()
    {
        $this->on(UserEvent::EVENT_BEFORE_REGISTER, function () {
            $this->username = $this->email;
        });

        $this->on(UserEvent::EVENT_BEFORE_CREATE, function () {
            $this->username = $this->email;
        });

        parent::init();
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['customerIdRequired'] = ['customer_id', 'required'];
        $rules['userTypeRequired'] = ['type', 'required'];
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

    public function getWarehouses()
    {
        return $this->hasMany(Warehouse::class, ['id' => 'warehouse_id'])
            ->viaTable(UserWarehouse::tableName(), ['user_id' => 'id']);
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
        if ($this->isWarehouseType()) {
            $data = Order::find()
                ->select('customer_id')
                ->distinct()
                ->where(['in', 'warehouse_id', $this->getWarehouseIds()])
                ->all();
            $data = \frontend\models\Customer::find()
                ->where(['id' => ArrayHelper::getColumn($data, 'customer_id')])
                ->orderBy([$valueField => SORT_ASC])
                ->all();
        } else {
            $data = $this->getCustomers()->orderBy([$valueField => SORT_ASC])->all();
        }

        return ArrayHelper::map($data, $keyField, $valueField);
    }

    public function getTypes()
    {
        return [self::TYPE_CUSTOMER => 'Customer', self::TYPE_WAREHOUSE => 'Warehouse'];
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
        $customer = \frontend\models\Customer::findOne($this->customer_id);
        return $customer->stripe_customer_id;
    }

    public function hasPaymentMethod()
    {
        return PaymentMethod::find()->where(['customer_id' => $this->customer_id])->exists();
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

    public function isWarehouseType()
    {
        return ($this->type === self::TYPE_WAREHOUSE);
    }

    public function isCustomerType()
    {
        return ($this->type === self::TYPE_CUSTOMER);
    }

    /**
     * Return the customer id
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomerId()
    {
        return $this->customer_id;
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

    /**
     * Get array of associated customers IDs
     *
     * @return array
     */
    public function getWarehouseIds()
    {
        return ArrayHelper::getColumn(
            $this->hasMany(UserWarehouse::class, ['user_id' => 'id'])
                ->select('warehouse_id')
                ->asArray()
                ->all(),
            'warehouse_id');
    }
}