<?php

namespace common\adapters;

use common\models\shipping\Service;
use common\models\Sku;
use yii\helpers\Json;

class ShopifyAdapter extends ECommerceAdapter
{
    public function __construct($orderJSON, $customer_id)
    {
        $json = Json::decode($orderJSON, true);

        parent::__construct($json, $customer_id);
    }

    protected function buildGeneral($json)
    {
        $this->customer_reference = str_replace('#', '', $json['name']);
        $this->UUID = (string)$json['id'];
        $this->origin = "Shopify";
        $this->notes = $json["tags"];
    }

    protected function buildAddress($json)
    {
        $this->shipToEmail = $json["email"];
        $this->shipToName = $json['shipping_address']['first_name'] . ' ' . $json['shipping_address']['last_name'];
        $this->shipToAddress1 = $json['shipping_address']['address1'];
        if (isset($json['shipping_address']['address2'])) {
            $this->shipToAddress2 = $json['shipping_address']['address2'];
        }
        $this->shipToCompany = $json['shipping_address']['company'];
        $this->shipToCity = $json['shipping_address']['city'];
        $this->shipToState = $json['shipping_address']['province'];
        $this->shipToZip = $json['shipping_address']['zip'];
        $this->shipToPhone = $json['shipping_address']['phone'];
        $this->shipToCountry = $json['shipping_address']['country_code'];
        if (isset($json["note"]) && !empty($json["note"])) {
            $this->orderNotes = $json["note"];
        }
    }

    protected function buildShipping($json)
    {
        if (!isset($json["shipping_lines"][0]["code"])) {
            $this->shippingService = Service::findOne(["shipwise_code" => "FedExGround"])->id;
            echo 'built shipping' . PHP_EOL;
            return;
        }

        switch ($json["shipping_lines"][0]["code"]) {
            case 'PRIORITY_OVERNIGHT':
                $this->shippingService = Service::findOne(["shipwise_code" => "FedExPriorityOvernight"])->id;
                break;
            case 'STANDARD_OVERNIGHT':
            case 'FedEx Standard Overnight':
                $this->shippingService = Service::findOne(["shipwise_code" => "FedExFirstOvernight"])->id;
                break;
            case 'FEDEX_2_DAY':
                $this->shippingService = Service::findOne(["shipwise_code" => "FedEx2Day"])->id;
                break;
            case 'GROUND_HOME_DELIVERY':
            case 'FEDEX_GROUND':
            default:
                $this->shippingService = Service::findOne(["shipwise_code" => "FedExGround"])->id;
                break;
        }
    }

    protected function buildItems($json)
    {
        $this->items = [];
        foreach ($json['line_items'] as $item) {
            if (!in_array($item['sku'], Sku::findall(['excluded' => 'true', 'customer_id' => $this->customerID])) && !empty(trim($item['sku']))) {
                $orderItem = [];
                $orderItem["name"] = $item['name'];
                $orderItem["quantity"] = $item['quantity'];
                $orderItem["sku"] = trim($item['sku']);
                $this->items[] = $orderItem;
            }
        }
    }
}