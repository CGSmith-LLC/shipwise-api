<?php


namespace common\adapters;


use common\models\Address;
use common\models\Order;
use common\models\State;

class ColdcoAdapter extends FulfillmentAdapter
{
    public const RENO_ID = 2;
    public const ST_LOUIS_ID = 1;

    public function getRequestInfo(Order $order): array
    {
        $config = [];

		$config = $this->buildGeneral($config, $order);
		$config = $this->buildCustomerID($config, $order->customer_id);
		$config = $this->buildFacilityID($config, $order->address->state);
        $config = $this->buildRoutingInfo($config, $order);
        $config = $this->buildShipTo($config, $order->address);
        $config = $this->buildSavedElements($config, $order);

		$event = new \CartonizationEvent();
		$event->customer_id = $order->customer_id;
		$event->items = $order->items;
        $this->trigger(self::EVENT_CARTONIZATION, $event);

        return $config;
    }

    private function buildCustomerID(array $arr, int $id): array
	{
		$arr['customerIdentifier']['id'] = $id; // Test Id. TODO: Get Coldco customer ID for each customer & handle switching
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

		return $arr;
	}

	private function buildRoutingInfo(array $arr, Order $order): array
	{
		$routingInfo = [];

		// TODO: Set info properly
		$routingInfo['carrier'] = $this->getCarrier($order->carrier_id);
		$routingInfo['mode'] = $this->getService($order->service_id);
		$routingInfo['account'] = "";

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

    	$arr['shipto'] = $shipto;

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
}