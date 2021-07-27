<?php

namespace common\services;

interface ECommerceService
{

    public function getOrders(): array;
    public function applyMeta();

    /**
     * TODO: Declare these methods when we need them
     * updateOrderStatus(order_id, status)
     * getOrderInfo(order_id)
     * updateOrderInfo(order_id, field, value)
     */

}