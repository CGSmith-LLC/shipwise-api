<?php


namespace common\adapters\fulfillment;


use CartonizationEvent;
use common\models\Address;
use common\models\Item;
use common\models\Order;
use common\models\shipping\Carrier;
use common\models\shipping\Service;
use common\models\State;
use stdClass;

class ColdcoAdapter extends BaseFulfillmentAdapter
{
	public const RENO_ID = 2;
	public const ST_LOUIS_ID = 1;

	public function getCreateOrderRequestInfo(Order $order, bool $deferNotification = false): array
	{
		$config = [];

		$config = $this->buildGeneral(arr: $config, order: $order);
		$config = $this->buildCustomerID(arr: $config, shipwiseID: $this->customer_id);
		$config = $this->buildFacilityID(arr: $config, state: $order->address->state);
		$config = $this->buildRoutingInfo(arr: $config, order: $order);
		$config = $this->buildShipTo(arr: $config, address: $order->address);
		$config = $this->buildSavedElements(arr: $config, order: $order);
		$config = $this->buildItems(arr: $config, items: $order->items);

		if ($deferNotification) {
			$config['defernotification'] = true;
		}

		$event = new CartonizationEvent();
		$event->customer_id = $order->customer_id;
		$event->items = $order->items;
		$this->trigger(self::EVENT_CARTONIZATION, $event);

		return $config;
	}

	private function buildCustomerID(array $arr, int $shipwiseID): array
	{
		switch ($shipwiseID) {
			default:
				$coldcoID = 28;
				break;
		}

		$arr['customerIdentifier']['id'] = $coldcoID; // Test Id. TODO: Get Coldco customer ID for each customer & handle switching
		return $arr;
	}

	private function buildFacilityID(array $arr, State $state): array
	{
		switch ($state->abbreviation) {
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
		$arr['shippingNotes'] = "";
		$arr['notes'] = $order->notes . ' ' . (is_null($order->origin) ? '' : $order->origin);

		if ($this->hasInfo($order->po_number)) $arr['PoNum'] = $order->po_number;

		return $arr;
	}

	private function buildRoutingInfo(array $arr, Order $order): array
	{
		$routingInfo = [];

		// TODO: Set info properly
		$routingInfo['carrier'] = $this->getCarrier(id: $order->carrier_id);
		$routingInfo['mode'] = $this->getService(id: $order->service_id);
		$routingInfo['account'] = "";

		if ($this->hasInfo($order->tracking)) {
			$routingInfo['TrackingNumber'] = $order->tracking;
		}

		if ($this->hasInfo($order->ship_from_zip)) {
			$routingInfo['shipPointZip'] = $order->ship_from_zip;
		}

		$arr['routingInfo'] = $routingInfo;
		return $arr;
	}

	private function getCarrier(int $id): string
	{
		$carrierIds = array_flip(array: Carrier::getShipwiseCodes());

		return match ($id) {
			$carrierIds['FedEx'] => 'FedEx',
			$carrierIds['UPS'] => 'UPS',
			//TODO: Add USPS, DHL eCommerce, OnTrack
		};
	}

	private function getService(int $id): string
	{
		$serviceIds = array_flip(array: Service::getShipwiseCodes());

		return match ($id) {
			$serviceIds['FedExGround'] => 92,
			$serviceIds['FedExPriorityOvernight'] => 01,
			$serviceIds['FedExStandardOvernight'] => 05,
			$serviceIds['FedEx2Day'] => 03,
			//TODO: Add the rest of the services
		};
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

		if ($this->hasInfo($address->email)) {
			$shipto['emailAddress'] = $address->email;
		}

		if ($this->hasInfo($address->address2)) {
			$shipto['emailAddress'] = $address->address2;
		}

		$arr['shipTo'] = $shipto;

		//if($address->isResidential()) $arr['UpsIsResidential'] = true;

		return $arr;
	}

	private function buildSavedElements(array $arr, Order $order): array
	{
		$origin = new stdClass();
		$origin->name = 'Origin';
		$origin->value = is_null($order->origin) ? 'Unknown' : $order->origin;

		$shipwise = new stdClass();
		$shipwise->name = 'Shipwise ID';
		$shipwise->value = $order->id;

		$transit = new stdClass();
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

		if (!empty($orderItems)) {
			$arr['orderItems'] = $orderItems;
		}

		return $arr;
	}

	private function hasInfo($var): bool
	{
		return !is_null($var) && !empty($var);
	}
}