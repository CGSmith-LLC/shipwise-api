<?php


namespace common\interfaces;


class ShopifyInterface extends ECommerceInterface
{

    public function getOrders(): array
    {
        $orderarray = [];

        /**
         * 1. Get all unfulfilled Shopify orders from the last 11 minutes (just to be safe)
         * 2. Extract all individual order objects-arrays from array
         * 3. Return
         */

        return $orderarray;
    }
}