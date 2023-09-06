<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subscription_webhook".
 *
 * @property int $id
 * @property string $payment_method
 * @property string $status
 * @property string $event
 * @property string $payload
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 */
class BaseSubscriptionWebhook extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscription_webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['payment_method', 'status', 'event', 'payload'], 'required'],
            [['payload', 'meta'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['payment_method', 'status', 'event'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'payment_method' => 'Payment Method',
            'status' => 'Status',
            'event' => 'Event',
            'payload' => 'Payload',
            'meta' => 'Meta',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }
}
