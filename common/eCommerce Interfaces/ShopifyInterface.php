<?php


namespace common\interfaces;


use yii\base\BaseObject;

class ShopifyInterface extends BaseObject implements ECommerceInterface
{

    public function getOrders(): array
    {
        $orderarray = [];

        /**
         * TODO: Make work
         * 1. Get all unfulfilled Shopify orders from the last 11 minutes (just to be safe)
         * 2. Extract all individual order object-arrays from array
         */

        return $orderarray;
    }
}