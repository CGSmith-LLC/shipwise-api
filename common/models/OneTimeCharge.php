<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "one_time_charge".
 *
 * @property int $id
 * @property int $customer_id Reference to customer
 * @property string $name
 * @property int $amount In cents
 * @property int $added_to_invoice
 */
class OneTimeCharge extends \yii\db\ActiveRecord
{

    public $decimalAmount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'one_time_charge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'name', 'amount'], 'required'],
            [['customer_id', 'added_to_invoice'], 'integer'],
            [['amount'], 'double', 'min' => 0],
            [['decimalAmount'], 'safe'],
            [['name'], 'string', 'max' => 128],
        ];
    }


    /**
     * Set from the form value
     *
     * @return int
     */
    public function setFromDecimalAmount()
    {
        return (int) ($this->decimalAmount * 100);
    }

    /**
     * Returns decimal amount after getting from database
     * @return float
     */
    public function getDecimalAmount()
    {
        return $this->amount / 100;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'name' => 'Name',
            'amount' => 'Amount',
            'added_to_invoice' => 'Added To Invoice',
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
