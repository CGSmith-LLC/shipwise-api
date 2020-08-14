<?php

namespace api\modules\v1\models\mappers;

use api\modules\v1\models\forms\AddressForm;
use api\modules\v1\models\forms\ItemForm;
use api\modules\v1\models\forms\OrderForm;
use api\modules\v1\models\order\StatusEx;
use common\models\Country;

class ShopifyMapper extends OrderForm
{

    public function parse($config = [])
    {
        $items = [];
        foreach ($config['line_items'] as $item) {
            $items[] = [
                'uuid' => (string)$item['id'],
                'name' => $item['title'],
                'sku' => $item['sku'],
                'quantity' => $item['quantity'],
            ];
        }

        /** @var Country $country */
        $country = Country::find()->where(['name' => $config['shipping_address']['country']])->one();

        $shipAddress = [
            'name' => $config['shipping_address']['name'],
            'company' => $config['shipping_address']['company'],
            //'email' => $config[''][''],
            'address1' => $config['shipping_address']['address1'],
            'address2' => $config['shipping_address']['address2'],
            'city' => $config['shipping_address']['city'],
            'state' => $config['shipping_address']['province'],
            'zip' => $config['shipping_address']['zip'],
            'phone' => $config['shipping_address']['phone'],
            'country' => $country->abbreviation,
        ];


        // Assign config var to Shopify request params
        $config = [
            'uuid' => (string)$config['id'],
            'notes' => $config['note'],
            'origin' => 'Shopify',
            'customerReference' => (string)$config['order_number'],
            'shipTo' => $shipAddress,
            'status' => StatusEx::OPEN,
            'items' => $items,

        ];

        return $config;
    }
}