<?php

namespace common\models\shipping\extension;

use Cassandra\Date;
use common\models\CustomerMeta;
use common\models\shipping\ShipmentPlugin;
use common\models\SpeedeeManifest;
use Yii;

class SpeeDeePlugin extends ShipmentPlugin
{
    /**
     * Plugin Name
     *
     * @var string Constant
     */
    const PLUGIN_NAME = "SpeeDee";

    private SpeedeeManifest $currentManifest;
    private string $customerNumber;


    public function autoload($customerId = null)
    {
        $customerMeta = CustomerMeta::find()
            ->where(['customer_id' => $customerId])
            ->andWhere(['key' => 'speedee_customer_number'])
            ->one();

        $this->customerNumber = $customerMeta->value;
    }

    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    /**
     *
     * @return mixed|void
     */
    protected function ratePrepare()
    {
        return $this;
    }

    protected function rateExecute()
    {
        return $this;
    }

    protected function rateProcess()
    {
        return $this;
    }

    protected function shipmentPrepare()
    {
        $manifest = new SpeedeeManifest();
        // Shipper Information
        $manifest->ship_from_shipper_number = $this->customerNumber;
        $manifest->ship_from_name           = $this->shipment->sender_company;
        $manifest->ship_from_address_1      = $this->shipment->sender_address1;
        $manifest->ship_from_address_2      = $this->shipment->sender_address2;
        $manifest->ship_from_city           = $this->shipment->sender_city;
        $manifest->ship_from_zip            = $this->shipment->sender_postal_code;
        $manifest->ship_from_country        = $this->shipment->sender_country;
        $manifest->ship_from_email          = $this->shipment->sender_email;
        $manifest->ship_from_phone          = $this->shipment->sender_phone;

        // Recipient Information
        $manifest->ship_to_import_field     = ''; // Would be the speedee internal recipient ID if we had it.
        $manifest->ship_to_shipper_number   = ''; // Probably wouldn't have this.
        $manifest->ship_to_name             = $this->shipment->recipient_company ?? $this->shipment->recipient_contact;
        $manifest->ship_to_attention        = ! $this->shipment->recipient_is_residential ? substr($this->shipment->recipient_contact, 0, 35) : '';
        $manifest->ship_to_address_1        = $this->shipment->recipient_address1;
        $manifest->ship_to_address_2        = $this->shipment->recipient_address2;
        $manifest->ship_to_city             = $this->shipment->recipient_city;
        $manifest->ship_to_country          = $this->shipment->recipient_country;
        $manifest->ship_to_email            = $this->shipment->recipient_email;
        $manifest->ship_to_phone            = $this->shipment->recipient_phone;
        $manifest->reference_1              = $this->shipment->customer_reference; // Additional Reference Field (Usually Invoice Number). 2, 3, 4 are also available for use.
        $manifest->weight                   = $this->shipment->getTotalWeight();

        $package = $this->shipment->getPackages()[0];
        $manifest->height                   = $package->height;
        $manifest->length                   = $package->length;
        $manifest->width                    = $package->width;

        $manifest->oversized                = false; // All package sizes are valid
        $manifest->pickup_tag               = ''; // TODO: find this?
        $manifest->aod                      = false; // TODO: Find signature required
        $manifest->aod_option               = 0; // TODO: Map int values to option
        $manifest->cod                      = false; // TODO: Find COD options
        $manifest->cod_value                = 0;
        $manifest->package_handling         = 0; // TODO: Package handling rate calc?
        $manifest->apply_package_handling   =  false;
        $manifest->ship_date                = date("Y-m-d H:i:s");
        $manifest->bill_to_shipper_number   = $this->customerNumber;
        $manifest->unboxed                  = false; // If our cartonization forgets to send a box, re-evaluate our lives

        $manifest->save();

        $this->currentManifest = $manifest;

        return $this;
    }

    protected function shipmentExecute()
    {
        Yii::$app->queue->push(new \SpeeDeeShipJob([
            'manifest' => $this->currentManifest,
            'index' => 0 // @TODO: solve the "how to generate this" dilemma
        ]));
    }

    protected function shipmentProcess()
    {
        return $this;
    }
}