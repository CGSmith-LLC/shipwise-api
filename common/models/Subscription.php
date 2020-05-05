<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscription".
 *
 * @property int $id
 * @property int $customer_id Reference to customer
 * @property string $next_invoice The Next Date to generate an invoice
 * @property int $months_to_recur How many months will be used to calculate the next invoice
 */
class Subscription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'next_invoice', 'months_to_recur'], 'required'],
            [['customer_id', 'months_to_recur'], 'integer'],
            [['next_invoice'], 'safe'],
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
            'next_invoice' => 'Next Invoice',
            'months_to_recur' => 'Months To Recur',
        ];
    }
}
