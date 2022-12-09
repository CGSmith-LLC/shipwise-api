<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "webhook_trigger".
 *
 * @property int $id
 * @property int $webhook_id
 * @property int $status_id
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Status $status
 * @property Webhook $webhook
 */
class WebhookTrigger extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'webhook_trigger';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['webhook_id', 'status_id', 'created_at', 'updated_at'], 'required'],
            [['webhook_id', 'status_id', 'created_at', 'updated_at'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::className(), 'targetAttribute' => ['status_id' => 'id']],
            [['webhook_id'], 'exist', 'skipOnError' => true, 'targetClass' => Webhook::className(), 'targetAttribute' => ['webhook_id' => 'id']],
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
            'status_id' => 'Status ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebhook()
    {
        return $this->hasOne(Webhook::className(), ['id' => 'webhook_id']);
    }
}
