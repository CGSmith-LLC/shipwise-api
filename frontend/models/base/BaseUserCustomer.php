<?php

namespace frontend\models\base;

/**
 * This is the model class for table "user_customer".
 *
 * @property int $id
 * @property int $user_id     Reference to user
 * @property int $customer_id Reference to customer
 */
class BaseUserCustomer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_customer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'customer_id'], 'required'],
            [['user_id', 'customer_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'customer_id' => 'Customer ID',
        ];
    }
}
