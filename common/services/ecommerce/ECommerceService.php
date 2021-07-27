<?php

namespace common\services\ecommerce;


use common\models\IntegrationMeta;

interface ECommerceService
{

    public function getOrders(): array;

    /** @param IntegrationMeta[] $metadata */
    public function applyMeta(array $metadata);

    /**
     * TODO: Declare these methods when we need them
     * updateOrderStatus(order_id, status)
     * getOrderInfo(order_id)
     * updateOrderInfo(order_id, field, value)
     */

}