<?php

namespace common\models\base;

use yii\base\Model;
use common\models\shipping\Shipment;

/**
 * ShipmentPackage model.
 *
 * @property int      $master_package_id   Package ID of the master package when shipment is MPS
 * @property int      $quantity            Quantity
 * @property string   $length              Length of the package in centimeters
 * @property string   $width               Width of the package in centimeters
 * @property string   $height              Height of the package in centimeters
 * @property string   $weight              Weight of the package in kilograms
 * @property string   $weight_invoiced     Invoice weight of the package in kilograms
 * @property string   $description         Description of the package content
 * @property string   $reference1          Package reference 1
 * @property string   $reference2          Package reference 2
 * @property string   $reference3          Package reference 3
 * @property string   $master_tracking_num Carrier tracking number
 * @property string   $tracking_num        Carrier master tracking number
 * @property string   $label_format        Format of the label file: PDF, PNG, EPL2, ZPL2, TEXT
 * @property string   $label_url           Full URL of the label file
 * @property string   $label_data          Label data encoded in base64
 * @property string   $tracking_url        Full URL courier tracking link including tracking number
 *
 * @property Shipment $shipment
 */
class BaseShipmentPackage extends Model
{

    public $quantity;
    public $length;
    public $width;
    public $height;
    public $weight;
    public $weight_invoiced;
    public $description;
    public $reference1;
    public $reference2;
    public $reference3;
    public $master_tracking_num;
    public $tracking_num;
    public $label_format;
    public $label_url;
    public $label_data;
    public $tracking_url;

    protected $shipment;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['master_package_id', 'quantity'], 'integer'],
            [['length', 'width', 'height', 'weight', 'weight_invoiced'], 'number'],
            [['label_data', 'label_url'], 'string'],
            [['description', 'tracking_url'], 'string', 'max' => 255],
            [['reference1', 'reference2', 'reference3', 'master_tracking_num', 'tracking_num'], 'string', 'max' => 64],
            [['label_format'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'master_package_id'   => 'Master Package ID',
            'quantity'            => 'Quantity',
            'length'              => 'Length',
            'width'               => 'Width',
            'height'              => 'Height',
            'weight'              => 'Weight',
            'weight_invoiced'     => 'Weight Invoiced',
            'description'         => 'Description',
            'reference1'          => 'Reference 1',
            'reference2'          => 'Reference 2',
            'reference3'          => 'Reference 3',
            'master_tracking_num' => 'Master Tracking Num',
            'tracking_num'        => 'Tracking Num',
            'label_format'        => 'Label Format',
            'label_url'           => 'Label URL',
            'label_data'          => 'Label data',
            'tracking_url'        => 'Tracking URL',
        ];
    }

    /**
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->shipment;
    }
}
