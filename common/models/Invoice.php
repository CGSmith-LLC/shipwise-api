<?php

namespace common\models;

use common\models\query\InvoiceQuery;
use frontend\models\PaymentIntent;

/**
 * This is the model class for table "invoice".
 *
 * @property int            $id
 * @property int            $customer_id      Reference to customer
 * @property int            $subscription_id  Reference to Subscription ID
 * @property string         $customer_name    Customer Name
 * @property int            $amount           Total in Cents
 * @property int            $balance          Balance Due in Cents
 * @property string         $due_date         Due Date
 * @property string         $stripe_charge_id stripe charge id
 * @property int            $status           Status of transaction
 *
 * @property Customer       $customer
 * @property InvoiceItems[] $items
 * @property Subscription[] $subscription
 * @property PaymentIntent  $paymentIntent
 */
class Invoice extends \yii\db\ActiveRecord
{
    public const STATUS_UNPAID = 1;
    public const STATUS_PAID   = 2;
    public const STATUS_LATE   = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['customer_id', 'subscription_id', 'customer_name', 'amount', 'balance', 'due_date', 'status'],
                'required',
            ],
            [['customer_id', 'subscription_id', 'amount', 'balance', 'status'], 'integer'],
            [['due_date'], 'safe'],
            [['customer_name'], 'string', 'max' => 64],
            [['stripe_charge_id'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'               => 'ID',
            'customer_id'      => 'Customer ID',
            'subscription_id'  => 'Subscription ID',
            'customer_name'    => 'Customer Name',
            'amount'           => 'Amount',
            'balance'          => 'Balance',
            'due_date'         => 'Due Date',
            'stripe_charge_id' => 'Stripe Charge ID',
            'status'           => 'Status',
        ];
    }

    /**
     * Returns decimal amount after getting from database
     *
     * @return float
     */
    public function getDecimalAmount()
    {
        return $this->amount / 100;
    }

    /**
     * Status label
     *
     * @param bool $html Whether to return in html format
     *
     * @return string
     */
    public function getStatusLabel($html = true)
    {
        $status = '';
        switch ($this->status) {
            case self::STATUS_UNPAID:
                $status = $html ? '<p class="label label-primary">Unpaid</p>' : 'Unpaid';
                break;
            case self::STATUS_PAID:
                $status = $html ? '<p class="label label-success">Paid</p>' : 'Paid';
                break;
            case self::STATUS_LATE:
                $status = $html ? '<p class="label label-danger">Past Due</p>' : 'Past Due';
                break;
        }

        return $status;
    }


    /**
     * Relation for InvoiceItems
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(InvoiceItems::class, ['invoice_id' => 'id']);
    }

    /**
     * Relation for Customers
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Relation for Subscriptions
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubscription()
    {
        return $this->hasMany(Subscription::class, ['id' => 'subscription_id']);
    }

    /**
     * Relation for PaymentIntent
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentIntent()
    {
        return $this->hasOne(PaymentIntent::class, ['invoice_id' => 'id']);
    }
}
