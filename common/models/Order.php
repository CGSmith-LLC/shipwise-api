<?php

namespace common\models;

use common\models\base\BaseOrder;
use common\models\query\OrderQuery;
use common\models\shipping\{Carrier, Service, PackageType, Shipment, ShipmentPackage};
use Yii;

/**
 * Class Order
 *
 * @package common\models
 *
 * @property Customer       $customer
 * @property Address        $address
 * @property TrackingInfo   $trackingInfo
 * @property Item[]         $items
 * @property Status         $status
 * @property OrderHistory[] $history
 * @property Carrier        $carrier
 * @property Service        $service
 */
class Order extends BaseOrder
{

    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }

    /**
     * Get Customer
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('common\models\Customer', ['id' => 'customer_id']);
    }

    /**
     * Get Ship To Address
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne('common\models\Address', ['id' => 'address_id']);
    }

    /**
     * Get Tracking Info
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrackingInfo()
    {
        return $this->hasOne('common\models\TrackingInfo', ['id' => 'tracking_id']);
    }

    /**
     * Get Order Items
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('common\models\Item', ['order_id' => 'id']);
    }

    /**
     * Get Order Status
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne('common\models\Status', ['id' => 'status_id']);
    }

    /**
     * Get Order History
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany('common\models\OrderHistory', ['order_id' => 'id']);
    }

    /**
     * Get carrier
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrier()
    {
        return $this->hasOne('common\models\shipping\Carrier', ['id' => 'carrier_id']);
    }

    /**
     * Get carrier service
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne('common\models\shipping\Service', ['id' => 'service_id']);
    }

    /**
     * Create shipment
     *
     *  - Builds Shipment object from Order data.
     *  - Applies predefined shipping options
     *  - Ships using Carrier API
     *  - Updates Order with obtained tracking info
     *  - Returns Shipment object containing PDF label as base64 encoded string
     *
     * @return Shipment|bool Shipment object on success or false on error
     * @throws \Exception
     */
    public function createShipment()
    {
        /**
         * Build Shipment object from Order data.
         */
        $shipment                = new Shipment();
        $shipment->shipment_date = new \DateTime("now");
        $shipment->customer_id   = $this->customer_id;

        // Sender
        $shipment->sender_contact        = $this->customer->name;
        $shipment->sender_company        = $this->customer->name;
        $shipment->sender_address1       = $this->customer->address1;
        $shipment->sender_address2       = $this->customer->address2;
        $shipment->sender_city           = $this->customer->city;
        $shipment->sender_state          = $this->customer->state->abbreviation;
        $shipment->sender_postal_code    = $this->customer->zip;
        $shipment->sender_country        = $this->customer->country;
        $shipment->sender_phone          = $this->customer->phone;
        $shipment->sender_email          = $this->customer->email;
        $shipment->sender_is_residential = false;

        // Recipient
        $shipment->recipient_contact     = $this->address->name;
        $shipment->recipient_address1    = $this->address->address1;
        $shipment->recipient_address2    = $this->address->address2;
        $shipment->recipient_city        = $this->address->city;
        $shipment->recipient_state       = $this->address->state->abbreviation;
        $shipment->recipient_postal_code = $this->address->zip;
        $shipment->recipient_country     = $this->address->country;
        $shipment->recipient_phone       = $this->address->phone;
        //$shipment->recipient_email = $this->address->; // @todo TBD
        //$shipment->recipient_is_residential = $this->address->; // @todo TBD

        // Packaging
        $shipment->package_type = PackageType::MY_PACKAGE;
        $shipment->weight_units = Shipment::WEIGHT_UNITS_LB;
        $shipment->dim_units    = Shipment::DIM_UNITS_IN;

        foreach ($this->items as $item) {
            $_pkg              = new ShipmentPackage();
            $_pkg->quantity    = $item->quantity;
            $_pkg->weight      = 5; // @todo TBD
            $_pkg->length      = 1; // @todo TBD
            $_pkg->width       = 1; // @todo TBD
            $_pkg->height      = 1; // @todo TBD
            $_pkg->description = $item->name;
            $shipment->addPackage($_pkg);
        }

        // Carrier & service codes
        $shipment->carrier = $this->carrier;
        $shipment->service = $this->service;
        if ($shipment->service && !$shipment->carrier) {
            $shipment->carrier = $this->service->carrier_id;
        }

        // Invoke carrier API call
        try {
            $shipment->ship();
        } catch (\Exception $e) {
            Yii::error($e);
            $shipment->addError('plugin', $e->getMessage());
        }

        // Shipment errors. This includes carrier API errors if any.
        if ($shipment->hasErrors()) {
            $this->addErrors($shipment->getErrors());
            return false;
        }

        return $shipment;
    }
}
