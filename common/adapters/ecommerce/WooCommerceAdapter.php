<?php

namespace common\adapters\ecommerce;

use common\models\shipping\Service;
use common\models\Sku;
use common\models\State;
use yii\helpers\Json;
use function GuzzleHttp\Promise\all;

class WooCommerceAdapter extends BaseECommerceAdapter
{
    public function __construct(string $orderJSON, int $customer_id)
    {
        $json = Json::decode(json: $orderJSON, asArray: true);

        parent::__construct(json: $json, customer_id: $customer_id);
    }

    protected function buildGeneral(array $json)
    {
        $this->customer_reference = $json['number'];
        $this->UUID = (string)$json['id'];
        $this->origin = "WooCommerce";
        $this->notes = $json['customer_note'];
    }

    protected function buildAddress(array $json)
    {
        $this->shipToEmail = $json['shipping']['email'] ?? $json['billing']['email'];
        $this->shipToPhone = $json['shipping']['phone'] ?? $json['billing']['phone'];
        $this->shipToName = $json['shipping']['first_name'] . ' ' . $json['shipping']['last_name'];
        $this->shipToAddress1 = $json['shipping']['address_1'];
        if (!empty($json['shipping']['address_2'])) {
            $this->shipToAddress2 = $json['shipping']['address_2'];
        }
        $this->shipToCompany = $json['shipping']['company'];
        $this->shipToCity = $json['shipping']['city'];
        $this->shipToState = State::findOne(['abbreviation' => $json['shipping']['state']])->id;
        $this->shipToZip = $json['shipping']['postcode'];
        $this->shipToCountry = $json['shipping']['country'];
    }

    protected function buildShipping(array $json)
    {
        $this->shippingService = Service::findByShipWiseCode('FedExGround')->id;
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