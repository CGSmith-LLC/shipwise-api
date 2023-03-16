<?php

namespace common\models;

use Yii;
use common\behaviors\OrderEventsBehavior;
use api\modules\v1\models\core\AddressEx;
use common\models\base\BaseOrder;
use common\models\query\OrderQuery;
use common\models\shipping\{Carrier, Service, PackageType, Shipment, ShipmentPackage};
use console\jobs\webhooks\OrderWebhook;

/**
 * Class Order
 *
 * @package common\models
 *
 * @property Customer       $customer
 * @property Address        $address
 * @property TrackingInfo   $trackingInfo
 * @property Item[]         $items
 * @property Package[]      $packages
 * @property Status         $status
 * @property OrderHistory[] $history
 * @property Carrier        $carrier
 * @property Service        $service
 */
class Order extends BaseOrder
{
    public function behaviors(): array
    {
        return [
            [
                'class' => OrderEventsBehavior::class,
            ],
        ];
    }

    public function init(): void
    {
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'createJobIfNeeded']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'createJobIfNeeded']);

        parent::init();
    }

    public function createJobIfNeeded($event)
    {
        // Only create a job if the status is changed
        // sender attribute needs to be cast as an int as it comes down as a string
        // if afterInsert - just make sure status_id is set as this is a newly created order
        if (($event->name == self::EVENT_AFTER_INSERT && isset($event->sender->status_id)) ||
            (isset($event->changedAttributes['status_id']) && $event->changedAttributes['status_id'] !== (int) $event->sender->status_id)) {
            // Only create a webhook that has a trigger set for the status
            $webhooks = Webhook::find()
                ->joinWith('webhookTrigger')
                ->where([
                    Webhook::tableName() . '.customer_id' => $event->sender->customer_id,
                    Webhook::tableName() . '.active' => Webhook::STATUS_ACTIVE,
                    WebhookTrigger::tableName() . '.status_id' => $event->sender->status_id,
                ])
                ->all();
            // loop through all webhooks to create a job
            if ($webhooks) {
                foreach ($webhooks as $webhook) {
                    \Yii::$app->queue->push(
                        new OrderWebhook([
                            'webhook_id' => $webhook->id,
                            'order_id' => $event->sender->id
                        ])
                    );
                }
            }
        }
    }

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
     * Return from address
     *
     * @return AddressEx
     */
    public function getFromAddress()
    {
        $address = new AddressEx();
        $address->name = isset($this->ship_from_name) ? $this->ship_from_name : $this->customer->name;
        $address->address1 = isset($this->ship_from_address1) ? $this->ship_from_address1 : $this->customer->address1;
        $address->address2 = isset($this->ship_from_address2) ? $this->ship_from_address2 : $this->customer->address2;
        $address->city = isset($this->ship_from_city) ? $this->ship_from_city : $this->customer->city;
        $address->state_id = isset($this->ship_from_state_id) ? $this->ship_from_state_id : $this->customer->state_id;
        $address->zip = isset($this->ship_from_zip) ? $this->ship_from_zip : $this->customer->zip;
        $address->country = isset($this->ship_from_country_code) ? $this->ship_from_country_code : $this->customer->country;
        $address->phone = isset($this->ship_from_phone) ? $this->ship_from_phone : $this->customer->phone;
        $address->email = isset($this->ship_from_email) ? $this->ship_from_email : $this->customer->email;

        return $address;
    }

    /**
     * Return an order reference after checking if it exists.
     *
     * @return string
     */
    public function getNextCustomerReferenceNumber()
    {
        $i = 1;
        $orderNumberToCheck = $this->customer_reference . '-' . $i;
        while (Order::find()
            ->where(['customer_reference' => $orderNumberToCheck])
            ->andWhere(['customer_id' => $this->customer_id])
            ->exists()) {
            $i++;
            $orderNumberToCheck = $this->customer_reference . '-' . $i;
        }

        return $orderNumberToCheck;
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
        return $this
            ->hasMany('common\models\OrderHistory', ['order_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
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
        $shipment = new Shipment();
        $shipment->shipment_date = new \DateTime("now");
        $shipment->customer_id = $this->customer_id;
        $shipment->order_id = $this->id;

// Sender
        /** @var AddressEx $sender */
        $sender = $this->getFromAddress();
        $shipment->sender_contact = $sender->name;
        $shipment->sender_company = $sender->name;
        $shipment->sender_address1 = $sender->address1;
        $shipment->sender_address2 = $sender->address2;
        $shipment->sender_city = $sender->city;
        $shipment->sender_state = $sender->state->abbreviation;
        $shipment->sender_postal_code = $sender->zip;
        $shipment->sender_country = $sender->country;
        $shipment->sender_phone = $sender->phone;
        $shipment->sender_email = $sender->email;
        $shipment->sender_is_residential = false;

// Recipient
        $shipment->recipient_contact = $this->address->name;
        $shipment->recipient_address1 = $this->address->address1;
        $shipment->recipient_address2 = $this->address->address2;
        $shipment->recipient_city = $this->address->city;
        $shipment->recipient_state = $this->address->state->abbreviation ?? '';
        $shipment->recipient_postal_code = $this->address->zip;
        $shipment->recipient_country = $this->address->country;
        $shipment->recipient_phone = $this->address->phone;
//$shipment->recipient_email = $this->address->; // @todo TBD
//$shipment->recipient_is_residential = $this->address->; // @todo TBD

// Packaging
        $shipment->package_type = PackageType::MY_PACKAGE;
        $shipment->weight_units = Shipment::WEIGHT_UNITS_LB;
        $shipment->dim_units = Shipment::DIM_UNITS_IN;

        foreach ($this->packages as $package) {
            $_pkg = new ShipmentPackage();
            $_pkg->quantity = 1; // @todo TBD
            $_pkg->weight = $package->weight;
            $_pkg->length = $package->length;
            $_pkg->width = $package->width;
            $_pkg->height = $package->height;
            $_pkg->description = 'Package'; // @todo TBD
            $_pkg->reference1 = $this->customer_reference;
            $_pkg->reference2 = $this->order_reference;
            $shipment->addPackage($_pkg);
        }

// Shipping carrier & service
        $shipment->service = $this->service;
        $shipment->carrier = $this->service->carrier;

// Shipment References
        $shipment->reference1 = $this->customer_reference;
        $shipment->reference2 = $this->order_reference;

// Retrieve existing label for this order, if exists
        if ($this->service->carrier->getReprintBehaviour() == Carrier::REPRINT_BEHAVIOUR_EXISTING) {
            if (!empty($this->tracking) && !empty($this->label_data) && !empty($this->label_type)) {
                $shipment->setMasterTrackingNumber($this->tracking);
                $shipment->setMergedLabelsData($this->label_data);
                $shipment->setMergedLabelsFormat($this->label_type);
                return $shipment;
            }
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

    /* Get Order Packages
    *
    * @return \yii\db\ActiveQuery
    */
    public function getPackages()
    {
        return $this->hasMany('common\models\Package', ['order_id' => 'id']);
    }

    public function getPackageItems()
    {
        return $this->hasMany('common\models\PackageItem', ['package_id' => 'id'])
            ->via('packages');
    }

    public function getLotInfo()
    {
        return $this->hasMany(PackageItemLotInfo::class, ['id' => 'package_id'])
            ->via('packageItems');
    }

    /** {inheritdoc} */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert) {
            // Unset saved label data if carrier/service are changed.
            if ((isset($changedAttributes['carrier_id']) && (int)$changedAttributes['carrier_id'] != $this->carrier_id) ||
                (isset($changedAttributes['service_id']) && (int)$changedAttributes['service_id'] != $this->service_id)) {
                $this->label_data = null;
                $this->label_type = null;
                $this->save(false);
            }
        }
    }
}
