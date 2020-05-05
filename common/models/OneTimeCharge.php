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
            [['customer_id', 'amount', 'added_to_invoice'], 'integer'],
            [['name'], 'string', 'max' => 128],
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
            'name' => 'Name',
            'amount' => 'Amount',
            'added_to_invoice' => 'Added To Invoice',
        ];
    }
}
