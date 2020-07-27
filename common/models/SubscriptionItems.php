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
            [['name', 'amount'], 'required'],
            [['subscription_id'], 'integer'],
            [['amount'], 'double', 'min' => 0],
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
        return (int) ($this->amount * 100);
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
            'subscription_id' => 'Subscription ID',
            'name' => 'Name',
            'amount' => 'Amount',
        ];
    }

    /**
     * Relation to Subscription
     * @return \yii\db\ActiveQuery
     */
    public function getSubscription()
    {
        return $this->hasOne(Subscription::class, ['id' => 'subscription_id']);
    }
}
