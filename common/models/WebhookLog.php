<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "webhook_log".
 *
 * @property int $id
 * @property int $webhook_id
 * @property int $status_code
 * @property string $response
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Webhook $webhook
 */
class WebhookLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webhook_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['webhook_id', 'status_code'], 'required'],
            [['webhook_id', 'status_code', 'created_at', 'updated_at'], 'integer'],
            [['response'], 'string'],
            [['webhook_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webhook::class, 'targetAttribute' => ['webhook_id' => 'id']],
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'webhook_id' => 'Webhook ID',
            'status_code' => 'Status Code',
            'response' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebhook()
    {
        return $this->hasOne(Webhook::class, ['id' => 'webhook_id']);
    }
}
