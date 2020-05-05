<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subscription_items".
 *
 * @property int $id
 * @property int $subscription_id Reference to subscriptions
 * @property string $name
 * @property int $amount amount in cents
 */
class SubscriptionItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subscription_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['subscription_id', 'name', 'amount'], 'required'],
            [['subscription_id', 'amount'], 'integer'],
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
            'subscription_id' => 'Subscription ID',
            'name' => 'Name',
            'amount' => 'Amount',
        ];
    }
}
