<?php

namespace common\models\base;

/**
 * This is the model class for table "orders".
 *
 * @property int    $id
 * @property int    $customer_id
 * @property string $order_reference
 * @property string $customer_reference
 * @property int    $status_id
 * @property string $tracking
 * @property string $created_date
 * @property string $updated_date
 * @property int    $address_id
 * @property string $notes
 * @property string $uuid
 * @property string $origin
 * @property string $requested_ship_date
 * @property int    $carrier_id
 * @property int    $service_id
 */
class BaseOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'customer_reference', 'address_id'], 'required'],
            [['customer_id', 'status_id', 'address_id', 'carrier_id', 'service_id'], 'integer'],
            [['created_date', 'updated_date', 'carrier_id', 'service_id'], 'safe'],
            [['order_reference', 'tracking'], 'string', 'max' => 45],
            [['customer_reference', 'origin'], 'string', 'max' => 64],
            [['notes'], 'string', 'max' => 140],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'customer_id'        => 'Customer ID',
            'order_reference'    => 'Order Reference',
            'customer_reference' => 'Customer Reference',
            'status_id'          => 'Status ID',
            'carrier_id'         => 'Carrier ID',
            'service_id'         => 'Service ID',
            'tracking'           => 'Tracking',
            'created_date'       => 'Created Date',
            'updated_date'       => 'Updated Date',
            'address_id'         => 'Address ID',
            'notes'              => 'Notes',
            'origin'             => 'Origin of Order',
        ];
    }
}