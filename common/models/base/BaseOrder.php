<?php

namespace common\models\base;

use common\models\Warehouse;

/**
 * This is the model class for table "orders".
 *
 * @property int    $id
 * @property int    $customer_id
 * @property int    $warehouse_id
 * @property string $order_reference
 * @property string $customer_reference
 * @property int    $status_id
 * @property string $tracking
 * @property string $created_date
 * @property string $updated_date
 * @property int    $address_id
 * @property string $notes
 * @property string $po_number
 * @property string $uuid
 * @property string $origin
 * @property string $requested_ship_date
 * @property string $must_arrive_by_date
 * @property int    $carrier_id
 * @property int    $service_id
 * @property string $label_data Shipping labels file encoded in base64
 * @property string $label_type File type for label_data field.
 * @property string $ship_from_name
 * @property string $ship_from_address1
 * @property string $ship_from_address2
 * @property string $ship_from_city
 * @property string $ship_from_state_id
 * @property string $ship_from_zip
 * @property string $ship_from_country_code
 * @property string $ship_from_phone
 * @property string $ship_from_email
 * @property int $transit
 * @property string $packagingNotes
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
            [['customer_id', 'warehouse_id', 'status_id', 'address_id', 'carrier_id', 'service_id', 'transit'], 'integer'],
            [['created_date', 'updated_date', 'requested_ship_date', 'must_arrive_by_date', 'carrier_id', 'service_id', 'transit'], 'safe'],
            [['order_reference', 'tracking'], 'string', 'max' => 45],
            [['customer_reference', 'origin', 'uuid'], 'string', 'max' => 64],
            [['notes', 'packagingNotes'], 'string', 'max' => 6000],
            [['po_number'], 'string', 'max' => 64],
            [['label_data'], 'string'],
            [['label_type'], 'string', 'max' => 6],
            [
                [
                    'ship_from_name',
                    'ship_from_address1',
                    'ship_from_address2',
                    'ship_from_city',
                    'ship_from_state_id',
                    'ship_from_zip',
                    'ship_from_country_code',
                    'ship_from_phone',
                    'ship_from_email'
                ],
                'safe'
            ],

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
            'warehouse_id'       => 'Warehouse ID',
            'order_reference'    => 'WMS Order #',
            'customer_reference' => 'Customer Order #',
            'status_id'          => 'Status ID',
            'carrier_id'         => 'Carrier ID',
            'service_id'         => 'Service ID',
            'tracking'           => 'Tracking',
            'created_date'       => 'Created Date',
            'updated_date'       => 'Updated Date',
            'address_id'         => 'Address ID',
            'notes'              => 'Notes',
            'origin'             => 'Origin of Order',
            'label_data'         => 'Label Data',
            'label_type'         => 'Label Type',
        ];
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id' => 'warehouse_id']);
    }
}
