<?php

namespace common\models;

use Da\User\Model\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhooks".
 *
 * @property int $id
 * @property string $endpoint
 * @property int $authentication_type
 * @property string $user
 * @property string $pass
 * @property int $customer_id
 * @property string $when
 * @property int $active
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Customer $customer
 */
class Webhook extends \yii\db\ActiveRecord
{
   const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['endpoint', 'customer_id', 'user_id', 'when', 'active'], 'required'],
            [['authentication_type', 'user_id', 'customer_id', 'active', 'created_at', 'updated_at'], 'integer'],
            [['endpoint', 'user', 'pass', 'when'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'endpoint' => 'Endpoint',
            'authentication_type' => 'Authentication Type',
            'user' => 'User',
            'pass' => 'Pass',
            'customer_id' => 'Customer ID',
            'user_id' => 'User ID',
            'when' => 'When',
            'active' => 'Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getWebhookTrigger()
    {
        return $this->hasMany(WebhookTrigger::class, ['webhook_id' => 'id']);
    }
}
