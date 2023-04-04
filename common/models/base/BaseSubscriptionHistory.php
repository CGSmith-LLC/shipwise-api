<?php

namespace common\models\base;

use Yii;
use yii\db\{ActiveQuery, ActiveRecord};
use frontend\models\Customer;

/**
 * This is the model class for table "subscription_history".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $payment_method
 * @property string $payment_method_subscription_id
 * @property int $is_active
 * @property int $is_trial
 * @property string $status
 * @property double $paid_amount
 * @property string $paid_currency
 * @property string $plan_name
 * @property string $plan_interval
 * @property string $plan_period_start
 * @property string $plan_period_end
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 *
 * @property Customer $customer
 */
class BaseSubscriptionHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscription_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['customer_id', 'payment_method', 'payment_method_subscription_id', 'is_trial', 'status', 'paid_amount', 'paid_currency', 'plan_interval', 'plan_period_start', 'plan_period_end'], 'required'],
            [['customer_id', 'is_active', 'is_trial'], 'integer'],
            [['paid_amount'], 'number'],
            [['plan_period_start', 'plan_period_end', 'created_date', 'updated_date'], 'safe'],
            [['meta'], 'string'],
            [['payment_method', 'status'], 'string', 'max' => 64],
            [['payment_method_subscription_id', 'plan_name'], 'string', 'max' => 128],
            [['paid_currency'], 'string', 'max' => 4],
            [['plan_interval'], 'string', 'max' => 10],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'payment_method' => 'Payment Method',
            'payment_method_subscription_id' => 'Payment Method Subscription ID',
            'is_active' => 'Is Active',
            'is_trial' => 'Is Trial',
            'status' => 'Status',
            'paid_amount' => 'Paid Amount',
            'paid_currency' => 'Paid Currency',
            'plan_name' => 'Plan Name',
            'plan_interval' => 'Plan Interval',
            'plan_period_start' => 'Plan Period Start',
            'plan_period_end' => 'Plan Period End',
            'meta' => 'Meta',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
