<?php

namespace common\adapters\ecommerce;

use common\models\shipping\Service;
use common\models\Sku;
use common\models\State;
use yii\helpers\Json;

class ShopifyAdapter extends BaseECommerceAdapter
{
    public function __construct(string $orderJSON, int $customer_id)
    {
        $json = Json::decode(json: $orderJSON, asArray: true);

        parent::__construct(json: $json, customer_id: $customer_id);
    }

    protected function buildGeneral(array $json)
    {
        $this->customer_reference = str_replace(search:'#', replace: '', subject: $json['name']);
        $this->UUID = (string)$json['id'];
        $this->origin = "Shopify";
        $this->notes = $json["tags"];
    }

    protected function buildAddress(array $json)
    {
        $this->shipToEmail = $json["email"];
        $this->shipToName = $json['shipping_address']['first_name'] . ' ' . $json['shipping_address']['last_name'];
        $this->shipToAddress1 = $json['shipping_address']['address1'];
        if (isset($json['shipping_address']['address2'])) {
            $this->shipToAddress2 = $json['shipping_address']['address2'];
        }
        $this->shipToCompany = $json['shipping_address']['company'];
        $this->shipToCity = $json['shipping_address']['city'];
        $this->shipToState = State::findOne(['name' => $json['shipping_address']['province']])->id;
        $this->shipToZip = $json['shipping_address']['zip'];
        $this->shipToPhone = $json['shipping_address']['phone'];
        $this->shipToCountry = $json['shipping_address']['country_code'];
        if (isset($json["note"]) && !empty($json["note"])) {
            $this->orderNotes = $json["note"];
        }
    }

    protected function buildShipping(array $json)
    {
        if (!isset($json["shipping_lines"][0]["code"])) {
            $this->shippingService = Service::findOne(["shipwise_code" => "FedExGround"])->id;
            return;
        }

		$this->shippingService = match ($json["shipping_lines"][0]["code"]) {
			'PRIORITY_OVERNIGHT' => Service::findOne(["shipwise_code" => "FedExPriorityOvernight"])->id,
			'STANDARD_OVERNIGHT', 'FedEx Standard Overnight' => Service::findOne(["shipwise_code" => "FedExFirstOvernight"])->id,
			'FEDEX_2_DAY' => Service::findOne(["shipwise_code" => "FedEx2Day"])->id,
			default => Service::findOne(["shipwise_code" => "FedExGround"])->id,
		};
    }

    protected function buildItems(array $json)
    {
        $this->items = [];
        foreach ($json['line_items'] as $item) {
            if (!in_array($item['sku'], Sku::findAll(condition: ['excluded' => 'true', 'customer_id' => $this->customerID])) && !empty(trim($item['sku']))) {
                $orderItem = [];
                $orderItem["name"] = $item['name'];
                $orderItem["quantity"] = $item['quantity'];
                $orderItem["sku"] = trim($item['sku']);
                $this->items[] = $orderItem;
            }
        }
    }
}