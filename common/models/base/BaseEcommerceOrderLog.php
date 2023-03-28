<?php

namespace common\models\base;

use Yii;
use yii\db\{ActiveQuery, ActiveRecord};
use common\models\{EcommercePlatform, EcommerceIntegration, Order};

/**
 * This is the model class for table "ecommerce_order_log".
 *
 * @property int $id
 * @property int $platform_id
 * @property int $integration_id
 * @property int $original_order_id
 * @property int $internal_order_id
 * @property string $status
 * @property string $payload
 * @property string $meta
 * @property string $created_date
 * @property string $updated_date
 *
 * @property EcommerceIntegration $integration
 * @property Order $internalOrder
 * @property EcommercePlatform $platform
 */
class BaseEcommerceOrderLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'ecommerce_order_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['meta'], 'default', 'value' => null],
            [['platform_id', 'integration_id', 'original_order_id', 'status', 'payload'], 'required'],
            [['platform_id', 'integration_id', 'internal_order_id'], 'integer'],
            [['payload', 'meta'], 'string'],
            [['created_date', 'updated_date'], 'safe'],
            [['status'], 'string', 'max' => 64],
            [['original_order_id'], 'string', 'max' => 256],
            [['integration_id'], 'exist', 'skipOnError' => true, 'targetClass' => EcommerceIntegration::class, 'targetAttribute' => ['integration_id' => 'id']],
            [['internal_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['internal_order_id' => 'id']],
            [['platform_id'], 'exist', 'skipOnError' => true, 'targetClass' => EcommercePlatform::class, 'targetAttribute' => ['platform_id' => 'id']],
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
            'integration_id' => 'Integration ID',
            'original_order_id' => 'Original Order ID',
            'internal_order_id' => 'Internal Order ID',
            'status' => 'Status',
            'payload' => 'Payload',
            'meta' => 'Meta Data',
            'created_date' => 'Created Date',
            'updated_date' => 'Updated Date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getIntegration(): ActiveQuery
    {
        return $this->hasOne(EcommerceIntegration::class, ['id' => 'integration_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInternalOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'internal_order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPlatform(): ActiveQuery
    {
        return $this->hasOne(EcommercePlatform::class, ['id' => 'platform_id']);
    }
}
