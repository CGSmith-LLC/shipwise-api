<?php

namespace common\models\base;

use Yii;
use yii\db\{ActiveQuery, ActiveRecord};
use frontend\models\Customer;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $payment_method
 * @property string $payment_method_subscription_id
 * @property int $is_active
 * @property int $is_trial
 * @property string $status
 * @property string $plan_name
 * @property string $plan_info
 * @property string $plan_interval
 * @property string $current_period_start
 * @property string $current_period_end
 * @property int $unsync_usage_quantity
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 *
 * @property Customer $customer
 */
class BaseSubscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['customer_id', 'payment_method', 'payment_method_subscription_id', 'is_trial', 'status',
                'plan_interval', 'current_period_start', 'current_period_end'], 'required'],
            [['customer_id', 'unsync_usage_quantity'], 'integer'],
            [['is_active', 'is_trial'], 'boolean'],
            [['current_period_start', 'current_period_end', 'created_date', 'updated_date'], 'safe'],
            [['meta'], 'string'],
            [['payment_method', 'status'], 'string', 'max' => 64],
            [['payment_method_subscription_id', 'plan_name'], 'string', 'max' => 128],
            [['plan_info'], 'string', 'max' => 512],
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
            'plan_name' => 'Plan Name',
            'plan_info' => 'Plan Info',
            'plan_interval' => 'Plan Interval',
            'current_period_start' => 'Current Period Start',
            'current_period_end' => 'Current Period End',
            'unsync_usage_quantity' => 'Unsyced Usage Quantity',
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
