<?php


namespace common\adapters;


use common\models\Order;
use common\models\shipping\Carrier;
use common\models\Status;
use yii\base\BaseObject;

abstract class ECommerceAdapter extends BaseObject
{

    // TODO: Add types
    protected $referenceNumber;
    protected $UUID;
    protected $status;
    protected $origin;
    protected $notes;
    protected $orderNotes;
    protected $customerID;

    protected $shipToEmail;
    protected $shipToName;
    protected $shipToAddress1;
    protected $shipToAddress2;
    protected $shipToCompany;
    protected $shipToCity;
    protected $shipToState;
    protected $shipToZip;
    protected $shipToPhone;
    protected $shipToCountry;

    protected $shippingService;

    protected $items;

    public function __construct($json, $customer_id)
    {
        parent::__construct();

        $this->status = Status::OPEN;
        $this->customerID = $customer_id;

        $this->buildGeneral($json);
        $this->buildAddress($json);
        $this->buildShipping($json);
        $this->buildItems($json);
    }

    public function parse(): Order
    {

        $shipwiseOrder = new Order();
        $shipwiseOrder = self::setGeneralInfo($shipwiseOrder);
        $shipwiseOrder = self::setAddressInfo($shipwiseOrder);
        $shipwiseOrder = self::setShippingInfo($shipwiseOrder);
        $shipwiseOrder = self::setItemInfo($shipwiseOrder);

        return $shipwiseOrder;
    }

    private function setGeneralInfo($order)
    {
        $order->setReferenceNumber($this->referenceNumber);
        $order->setUUID($this->UUID);
        $order->setStatus(Status::OPEN);
        $order->setOrigin($this->origin);
        $order->setNotes($this->notes);
        if (isset($this->notes) && !empty($this->notes)) {
            $order->setOrderNotes($this->notes);
        }

        return $order;
    }

    private function setAddressInfo($order)
    {
        $order->setShipToEmail($this->shipToEmail);
        $order->setShipToName($this->shipToName);
        $order->setShipToAddress1($this->shipToAddress1);
        if (isset($this->shipToAddress2)) {
            $order->setShipToAddress2($this->shipToAddress2);
        }
        $order->setShipToCompany($this->shipToCompany);
        $order->setShipToCity($this->shipToCity);
        $order->setShipToState($this->shipToState);
        $order->setShipToZip($this->shipToZip);
        $order->setShipToPhone($this->shipToPhone);
        $order->setShipToCountry($this->shipToCountry);

        return $order;
    }

    private function setShippingInfo($order)
    {
        $order->setShipCarrier(Carrier::findOne(["name" => "FedEx"]));

        $order->setShipService($this->shippingService);

        return $order;
    }

    private function setItemInfo($order)
    {
        $order->setOrderedItems($this->items);

        return $order;
    }

    protected abstract function buildGeneral($json);
    protected abstract function buildAddress($json);
    protected abstract function buildShipping($json);
    protected abstract function buildItems($json);
}