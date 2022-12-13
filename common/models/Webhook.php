<?php

namespace common\models;

use Da\User\Model\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhooks".
 *
 * @property int $id
 * @property string $name
 * @property string $endpoint
 * @property int $authentication_type
 * @property string $signing_secret
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

   const NO_AUTH = 0;
   const BASIC_AUTH = 1;
   const HEADER_AUTH = 2;

   public array $authenticationOptions = [
       self::NO_AUTH => 'None',
       self::BASIC_AUTH => 'Basic Auth',
       self::HEADER_AUTH => 'Header Auth',
   ];

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
            [['endpoint', 'name', 'customer_id', 'user_id', 'when', 'active', 'signing_secret'], 'required'],
            [['authentication_type', 'user_id', 'customer_id', 'active', 'created_at', 'updated_at'], 'integer'],
            [['endpoint', 'name', 'user', 'pass', 'when', 'signing_secret'], 'string', 'max' => 255],
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
            'name' => 'Name',
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

    public function getWebhookLog()
    {
        return $this->hasMany(WebhookLog::class, ['webhook_id' => 'id']);
    }

    public function getLabelFor($attribute)
    {
        $template = '<span class="label label-info">%s</span>';
        if ($attribute == 'authentication_type') {
            return match ($this->$attribute) {
                self::HEADER_AUTH, self::BASIC_AUTH => sprintf($template,  $this->authenticationOptions[$this->$attribute]),
                default => '',
            };
        }

        if (isset($this->$attribute)) {
            return sprintf($template, $this->$attribute);
        }

        // return empty string if nothing to match
        return '';
    }
}
