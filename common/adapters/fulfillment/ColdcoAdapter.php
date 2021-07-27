<?php


namespace common\adapters\fulfillment;


use common\models\Address;
use common\models\Item;
use common\models\Order;
use common\models\State;
use function PHPUnit\Framework\isNull;

class ColdcoAdapter extends FulfillmentAdapter
{
    public const RENO_ID = 2;
    public const ST_LOUIS_ID = 1;

    public function getRequestInfo(Order $order, bool $deferNotification = false): array
    {
        $config = [];

		$config = $this->buildGeneral($config, $order);
		$config = $this->buildCustomerID($config, $order->customer_id);
		$config = $this->buildFacilityID($config, $order->address->state);
        $config = $this->buildRoutingInfo($config, $order);
        $config = $this->buildShipTo($config, $order->address);
        $config = $this->buildSavedElements($config, $order);
        $config = $this->buildItems($config, $order->items);

        if($deferNotification) {
        	$config['defernotification'] = true;
		}

		$event = new \CartonizationEvent();
		$event->customer_id = $order->customer_id;
		$event->items = $order->items;
        $this->trigger(self::EVENT_CARTONIZATION, $event);

        return $config;
    }

    private function buildCustomerID(array $arr, int $shipwiseID): array
	{
		switch($shipwiseID)
		{
			default:
				$coldcoID = 28;
				break;
		}

		$arr['customerIdentifier']['id'] = $coldcoID; // Test Id. TODO: Get Coldco customer ID for each customer & handle switching
		return $arr;
	}

	private function buildFacilityID(array $arr, State $state): array
	{
		switch($state->abbreviation)
		{
			case 'CA':
			case 'OR':
			case 'WA':
			case 'ID':
			case 'UT':
			case 'AZ':
			case 'AK':
			case 'HI':
			case 'NV':
			$arr['facilityIdentifier']['id'] = self::RENO_ID;
				break;
			default:
				$arr['facilityIdentifier']['id'] = self::ST_LOUIS_ID;
				break;
		}

		return $arr;
	}

	private function buildGeneral(array $arr, Order $order): array
	{
		//	Todo: Set properly
		$arr['referenceNum'] = $order->customer_reference;
		$arr['billingCode'] = "";// Billing Info ????
		$arr['earliestShipDate'] = "";//????
		$arr['shipCancelDate'] = "";//????
		$arr['shippingNotes'] = "";// Carrier Notes different from notes ????
		//if($this->hasInfo($order->purchaseOrder)) $arr['PoNum'] = $order->purchaseOrder

		return $arr;
	}

	private function buildRoutingInfo(array $arr, Order $order): array
	{
		$routingInfo = [];

		// TODO: Set info properly
		$routingInfo['carrier'] = $this->getCarrier($order->carrier_id);
		$routingInfo['mode'] = $this->getService($order->service_id);
		$routingInfo['account'] = "";

		if($this->hasInfo($order->tracking)) {
			$routingInfo['TrackingNumber'] = $order->tracking;
		}

		if($this->hasInfo($order->ship_from_zip)) {
			$routingInfo['shipPointZip'] = $order->ship_from_zip;
		}

		$arr['routingInfo'] = $routingInfo;
		return $arr;
	}

	private function getCarrier(int $id): string
	{
		return "";
	}

	private function getService(int $id): string
	{
		return "";
	}

	private function buildShipTo(array $arr, Address $address): array
	{
    	$shipto = [];

    	$shipto['companyName'] = $address->company;
    	$shipto['name'] = $address->name;
    	$shipto['address1'] = $address->address1;
    	$shipto['city'] = $address->city;
    	$shipto['state'] = $address->state->name;
    	$shipto['zip'] = $address->zip;
    	$shipto['country'] = $address->country;
    	$shipto['phoneNumber'] = $address->phone;

    	if($this->hasInfo($address->email)) {
			$shipto['emailAddress'] = $address->email;
		}

    	if($this->hasInfo($address->address2)) {
			$shipto['emailAddress'] = $address->address2;
		}

		$arr['shipTo'] = $shipto;

		//if($address->isResidential()) $arr['UpsIsResidential'] = true;

		return $arr;
	}

	private function buildSavedElements(array $arr, Order $order): array
	{
		$origin = new \stdClass();
		$origin->name = 'Origin';
		$origin->value = is_null($order->origin) ? 'Unknown' : $order->origin;

		$shipwise = new \stdClass();
		$shipwise->name = 'Shipwise ID';
		$shipwise->value = $order->id;

		$transit = new \stdClass();
		$transit->name = 'Est Transit';
		//$transit->value = is_null($order->????) ? 'Unknown' : $order->????; TODO: Transit time

    	$savedElements = [
    		$origin,
			$shipwise,
			$transit,
		];

    	$arr['savedElements'] = $savedElements;

		return $arr;
	}

	/** @var Item[] $items */
	private function buildItems(array $arr, array $items): array
	{
		$orderItems = [];

		$iItem = 0;

		foreach ($items as $item) {
			$orderItems[$iItem]['itemIdentifier']['sku'] = $item->sku;
			$orderItems[$iItem]['qty'] = $item->quantity;
			//if(!isNull($item->????)) $orderItems[$iItem]['serialNumber'] = $item->???? TODO: Serial number?
			$iItem++;
		}

		if(!empty($orderItems)) {
			$arr['orderItems'] = $orderItems;
		}

		return $arr;
	}

	private function hasInfo($var): bool
	{
		return !is_null($var) && !empty($var);
	}
}