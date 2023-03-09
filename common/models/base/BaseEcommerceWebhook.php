<?php

namespace common\models\base;

use yii\db\{ActiveQuery, ActiveRecord};
use common\models\{EcommercePlatform};

/**
 * This is the model class for table "ecommerce_webhook".
 *
 * @property int $id
 * @property int $platform_id
 * @property string $status
 * @property string $event
 * @property string $payload
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 *
 * @property EcommercePlatform $platform
 */
class BaseEcommerceWebhook extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'ecommerce_webhook';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['platform_id', 'status', 'event', 'payload'], 'required'],
            [['platform_id'], 'integer'],
            [['payload', 'meta'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['status', 'event'], 'string', 'max' => 64],
            [['platform_id'], 'exist', 'skipOnError' => true, 'targetClass' => EcommercePlatform::className(), 'targetAttribute' => ['platform_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'platform_id' => 'Platform ID',
            'status' => 'Status',
            'event' => 'Event',
            'payload' => 'Payload',
            'meta' => 'Meta',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPlatform(): ActiveQuery
    {
        return $this->hasOne(EcommercePlatform::className(), ['id' => 'platform_id']);
    }
}
