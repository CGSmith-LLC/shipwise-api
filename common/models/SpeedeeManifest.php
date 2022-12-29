<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "speedee_manifests".
 *
 * @property int $id
 * @property int $order_id
 * @property int $customer_id
 * @property string $ship_from_shipper_number
 * @property string $ship_from_name
 * @property string $ship_from_attention
 * @property string $ship_from_address_1
 * @property string $ship_from_address_2
 * @property string $ship_from_city
 * @property int $ship_from_zip
 * @property string $ship_from_country
 * @property string $ship_from_email
 * @property string $ship_from_phone
 * @property string $ship_to_import_field
 * @property string $ship_to_shipper_number
 * @property string $ship_to_name
 * @property string $ship_to_attention
 * @property string $ship_to_address_1
 * @property string $ship_to_address_2
 * @property string $ship_to_city
 * @property string $ship_to_country
 * @property string $ship_to_email
 * @property string $ship_to_phone
 * @property string $reference_1
 * @property string $reference_2
 * @property string $reference_3
 * @property string $reference_4
 * @property int $weight
 * @property int $length
 * @property int $width
 * @property int $height
 * @property string $barcode
 * @property int $oversized
 * @property int $pickup_tag
 * @property int $aod
 * @property int $aod_option
 * @property int $cod
 * @property int $cod_value
 * @property int $declared_value
 * @property int $package_handling
 * @property int $apply_package_handling
 * @property string $ship_date
 * @property string $bill_to_shipper_number
 * @property int $unboxed
 * @property string $manifest_filename
 * @property boolean $is_manifest_sent
 *
 * @property Customer $customer
 * @property Order $order
 */
class SpeedeeManifest extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'speedee_manifests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'customer_id', 'ship_from_zip', 'weight', 'length', 'width', 'height', 'oversized', 'pickup_tag', 'aod', 'aod_option', 'cod', 'cod_value', 'declared_value', 'package_handling', 'apply_package_handling', 'unboxed'], 'integer'],
            [['ship_date'], 'safe'],
            [['ship_from_shipper_number', 'ship_to_shipper_number', 'bill_to_shipper_number'], 'string', 'max' => 6],
            [['ship_from_name', 'ship_from_attention', 'ship_from_address_1', 'ship_from_address_2', 'ship_from_city', 'ship_from_country', 'ship_from_email', 'ship_from_phone', 'ship_to_import_field', 'ship_to_name', 'ship_to_attention', 'ship_to_address_1', 'ship_to_address_2', 'ship_to_city', 'ship_to_country', 'ship_to_email', 'ship_to_phone', 'reference_1', 'reference_2', 'reference_3', 'reference_4', 'barcode'], 'string', 'max' => 255],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'customer_id' => 'Customer ID',
            'ship_from_shipper_number' => 'Ship From Shipper Number',
            'ship_from_name' => 'Ship From Name',
            'ship_from_attention' => 'Ship From Attention',
            'ship_from_address_1' => 'Ship From Address  1',
            'ship_from_address_2' => 'Ship From Address  2',
            'ship_from_city' => 'Ship From City',
            'ship_from_zip' => 'Ship From Zip',
            'ship_from_country' => 'Ship From Country',
            'ship_from_email' => 'Ship From Email',
            'ship_from_phone' => 'Ship From Phone',
            'ship_to_import_field' => 'Ship To Import Field',
            'ship_to_shipper_number' => 'Ship To Shipper Number',
            'ship_to_name' => 'Ship To Name',
            'ship_to_attention' => 'Ship To Attention',
            'ship_to_address_1' => 'Ship To Address  1',
            'ship_to_address_2' => 'Ship To Address  2',
            'ship_to_city' => 'Ship To City',
            'ship_to_country' => 'Ship To Country',
            'ship_to_email' => 'Ship To Email',
            'ship_to_phone' => 'Ship To Phone',
            'reference_1' => 'Reference  1',
            'reference_2' => 'Reference  2',
            'reference_3' => 'Reference  3',
            'reference_4' => 'Reference  4',
            'weight' => 'Weight',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'barcode' => 'Barcode',
            'oversized' => 'Oversized',
            'pickup_tag' => 'Pickup Tag',
            'aod' => 'Aod',
            'aod_option' => 'Aod Option',
            'cod' => 'Cod',
            'cod_value' => 'Cod Value',
            'declared_value' => 'Declared Value',
            'package_handling' => 'Package Handling',
            'apply_package_handling' => 'Apply Package Handling',
            'ship_date' => 'Ship Date',
            'bill_to_shipper_number' => 'Bill To Shipper Number',
            'unboxed' => 'Unboxed',
            'manifest_filename' => 'Manifest Filename',
            'is_manifest_sent' => 'Manifest is Sent',
            'checksum' => 'Checksum',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne('common\models\Order', ['id' => 'order_id']);
    }
}
