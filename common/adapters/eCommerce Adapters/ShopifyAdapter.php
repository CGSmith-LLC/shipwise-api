<?php

namespace common\adapters;

use common\models\Order;
use common\models\Status;
use \yii\base\BaseObject;
use yii\helpers\Json;

class ShopifyAdapter extends BaseObject
{

    public static function parse($orderJSON): Order
    {
        $shopifyOrder = Json::decode($orderJSON, true);

        $shipwiseOrder = new Order();
        $shipwiseOrder = self::setGeneralInfo($shipwiseOrder, $shopifyOrder);
        $shipwiseOrder = self::setAddressInfo($shipwiseOrder, $shopifyOrder);
        $shipwiseOrder = self::setShippingInfo($shipwiseOrder, $shopifyOrder);
        $shipwiseOrder = self::setItemInfo($shipwiseOrder, $shopifyOrder);

        return $shipwiseOrder;
    }

    static function setGeneralInfo($order, $json)
    {
        $order->setReferenceNumber(str_replace('#', '', $json['name']));
        $order->setUUID((string)$json['id']);
        $order->setStatus(Status::OPEN);
        $order->setOrigin("Shopify");
        $order->setNotes($json["tags"]);
        if (isset($json["note"]) && !empty($json["note"])) {
            $order->setOrderNotes($json["note"]);
        }

        return $order;
    }

    static function setAddressInfo($order, $json)
    {
        $order->setShipToEmail($json["email"]);
        $order->setShipToName($json['shipping_address']['first_name'] . ' ' . $json['shipping_address']['last_name']);
        $order->setShipToAddress1($json['shipping_address']['address1']);
        if (isset($json['shipping_address']['address2'])) {
            $order->setShipToAddress2($json['shipping_address']['address2']);
        }
        $order->setShipToCompany($json['shipping_address']['company']);
        $order->setShipToCity($json['shipping_address']['city']);
        $order->setShipToState($json['shipping_address']['province']);
        $order->setShipToZip($json['shipping_address']['zip']);
        $order->setShipToPhone($json['shipping_address']['phone']);
        if ($json['shipping_address']['country_code'] !== 'US') {
            $order->setShipToCountry($json['shipping_address']['country_code']);
        }

        return $order;
    }

    static function setShippingInfo($order, $json)
    {
        $order->setShipCarrier();

        $order->setShipService();

        return $order;
    }

    static function setItemInfo($order, $json)
    {
       /* foreach ($json['line_items'] as $item) {
            if (!in_array($item['sku'], $this->excludedProducts) && !empty(trim($item['sku']))) {
                $orderItem = new OrderedItem();
                $orderItem->setName($item['name']);
                $orderItem->setQuantity($item['quantity']);
                $orderItem->setSku(trim($item['sku']));
                $order['items'][] = $orderItem;
            }
        }
        if (!isset($json['items'])) {
            echo 'No items for ' . $json['name'] . PHP_EOL;
        } else {
            if (count($json['items'])) {
                $order->setOrderedItems($json['items']);
                return $order;
            }
        }*/

        return $order;
    }
}