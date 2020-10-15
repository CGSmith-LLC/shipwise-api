<?php

namespace frontend\models;

use common\models\PaymentMethod;

/**
 * This is the model class for table "payment_intent".
 *
 * @property int $id
 * @property int $customer_id Reference to customer
 * @property int $payment_method_id Reference to payment method table
 * @property int $invoice_id
 * @property string $stripe_payment_intent_id stripe payment intent id
 * @property int $amount Total in Cents
 * @property string $status Stripe Status of Payment Intent
 * @property string $created_date Created Date
 *
 * @property PaymentMethod $paymentMethod
 */
class PaymentIntent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_intent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'payment_method_id', 'invoice_id', 'amount', 'status'], 'required'],
            [['customer_id', 'payment_method_id', 'invoice_id', 'amount'], 'integer'],
            [['created_date'], 'safe'],
            [['stripe_payment_intent_id'], 'string', 'max' => 128],
            [['status'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'payment_method_id' => 'Payment Method ID',
            'invoice_id' => 'Invoice ID',
            'stripe_payment_intent_id' => 'Stripe Payment Intent ID',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * Relation for PaymentMethod
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class, ['id' => 'payment_method_id']);
    }
}
