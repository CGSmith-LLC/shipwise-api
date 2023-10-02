<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

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
            [['webhook_id', 'status_id'], 'required'],
            [['webhook_id', 'status_id', 'created_at', 'updated_at'], 'integer'],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => Status::class, 'targetAttribute' => ['status_id' => 'id']],
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
        return $this->hasOne(Status::class, ['id' => 'status_id']);
    }

    public function getName()
    {
        return $this->getStatus()->one()->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWebhook()
    {
        return $this->hasOne(Webhook::class, ['id' => 'webhook_id']);
    }
}
