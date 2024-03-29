<?php

namespace api\modules\v1\models\order;

use api\modules\v1\models\core\AddressEx;
use common\models\Order;
use common\models\Package;
use common\models\PackageItemLotInfo;

/**
 * Class OrderEx
 *
 * @package api\modules\v1\models\order
 */
class OrderEx extends Order
{

    /**
     * @SWG\Definition(
     *     definition = "Order",
     *
     *     @SWG\Property( property = "id", type = "integer", description = "Order ID" ),
     *     @SWG\Property( property = "carrier_id", type = "integer", description = "Specifies Carrier to ship through"
     *                    ),
     *     @SWG\Property( property = "carrier_name", type = "string", description = "Specifies Carrier name" ),
     *     @SWG\Property( property = "service_id", type = "integer", description = "Specifies Service level to ship
    through" ),
     *     @SWG\Property( property = "service_name", type = "string", description = "Specifies Service name" ),
     *     @SWG\Property( property = "orderReference", type = "string", description = "Order reference - Order number
    from fulfillment side" ),
     *     @SWG\Property( property = "customerReference", type = "string", description = "Customer reference - Order
    Number from ecommerce side" ),
     *     @SWG\Property( property = "requestedShipDate", type = "string", format = "date-time", description = "When
    the order should ship and be fulfilled" ),
     *     @SWG\Property( property = "mustArriveByDate", type = "string", format = "date-time", description = "When
    the order should arrive at the customer" ),
     *     @SWG\Property( property = "shipFrom", ref = "#/definitions/Address" ),
     *     @SWG\Property( property = "shipTo", ref = "#/definitions/Address" ),
     *     @SWG\Property( property = "tracking", ref = "#/definitions/TrackingInfo" ),
     *     @SWG\Property(
     *          property = "items",
     *          type = "array",
     *          @SWG\Items( ref = "#/definitions/Item" )
     *     ),
     *     @SWG\Property(
     *          property = "packages",
     *          type = "array",
     *          @SWG\Items( ref = "#/definitions/Package Shipped" )
     *     ),
     *     @SWG\Property( property = "createdDate", type = "string", format = "date-time" ),
     *     @SWG\Property( property = "updatedDate", type = "string", format = "date-time" ),
     *     @SWG\Property( property = "status", ref = "#/definitions/Status" ),
     *     @SWG\Property( property = "customer", ref = "#/definitions/Customer" ),
     *     @SWG\Property( property = "poNumber", type = "string", description = "PO Number of ecommerce customer" ),
     *     @SWG\Property( property = "uuid", type = "string", description = "Reference to ecommerce UUID" ),
     *     @SWG\Property( property = "notes", type = "string", description = "Notes specific to an order" ),
     *     @SWG\Property( property = "origin", type = "string", description = "Origination of order. Such as
    SquareSpace or Zoho" ),
     *     @SWG\Property( property = "transit", type = "integer", description = "Days in transit if available" ),
     *     @SWG\Property( property = "packagingNotes", type = "string", description = "Description of packaging for warehouse" ),
     * )
     */

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id'                => 'id',
            'orderReference'    => 'order_reference',
            'customerReference' => 'customer_reference',
            'requestedShipDate' => 'requested_ship_date',
            'mustArriveByDate'  => 'must_arrive_by_date',
            'shipFrom'          => 'fromAddress',
            'shipTo'            => 'address',
            'tracking'          => 'trackingInfo',
            'items'             => 'items',
            'createdDate'       => 'created_date',
            'updatedDate'       => 'updated_date',
            'status'            => 'status',
            'customer'          => 'customer',
            'carrier_id'        => 'carrier_id',
            'carrier_name'      => function () {
                return isset($this->carrier) ? $this->carrier['name'] : '';
            },
            'service_id'        => 'service_id',
            'service_name'      => function () {
                return isset($this->service) ? $this->service['name'] : '';
            },
            'poNumber'          => 'po_number',
            'uuid'              => 'uuid',
            'notes'             => 'notes',
            'origin'            => 'origin',
            'packages'          => 'packages',
            'transit'           => 'transit',
            'packagingNotes'    => 'packagingNotes',
        ];
    }

    /**
     * Get Customer
     *
     * Overwrite parent method to use CustomerEx
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne('api\modules\v1\models\customer\CustomerEx', ['id' => 'customer_id']);
    }

    /**
     * Get Order Status
     *
     * Overwrite parent method to use StatusEx
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne('api\modules\v1\models\order\StatusEx', ['id' => 'status_id']);
    }

    /**
     * Get packages from the order
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPackages()
    {
        return $this->hasMany('api\modules\v1\models\order\PackageEx', ['order_id' => 'id']);
    }


    /**
     * Get Items
     *
     * Overwrite parent method to use OrderHistoryEx
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany('api\modules\v1\models\order\ItemEx', ['order_id' => 'id']);
    }

    /**
     * Get Ship To Address
     *
     * Overwrite parent method to use AddressEx
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne('api\modules\v1\models\core\AddressEx', ['id' => 'address_id']);
    }

    /**
     * Get Carrier Name
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarrier()
    {
        return $this->hasOne('api\modules\v1\models\core\CarrierEx', ['id' => 'carrier_id']);
    }

    /**
     * Get service name
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne('api\modules\v1\models\core\ServiceEx', ['id' => 'service_id']);
    }

    /**
     * Get Tracking Info
     *
     * @return \yii\db\ActiveQuery
     * @todo This method is done in prevision of the future implementation of TrackingInfo relation.
     *       As for now, and before that transition happen, this method will imitate the return of a TrackingInfoEx
     *       object but only `TrackingInfoEx.trackingNumber` property will be populated.
     *
     */
    public function getTrackingInfo()
    {
        // Uncomment this line to return TrackingInfoEx relation, and remove everything after this line.
        // return $this->hasOne('api\modules\v1\models\order\TrackingInfoEx', ['id' => 'tracking_id']);

        $trackingInfo           = new TrackingInfoEx();
        $trackingInfo->tracking = $this->tracking;

        return $trackingInfo;
    }
}