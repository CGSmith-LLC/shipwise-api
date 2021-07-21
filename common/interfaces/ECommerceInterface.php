<?php

namespace common\interfaces;

interface ECommerceInterface
{
    public function getOrders(): array;

    /**
     * TODO: Declare these methods when we need them
     * updateOrderStatus(order_id, status)
     * getOrderInfo(order_id)
     * updateOrderInfo(order_id, field, value)
     */

}