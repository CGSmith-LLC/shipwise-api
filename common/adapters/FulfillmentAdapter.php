<?php


namespace common\adapters;

use common\models\Order;

interface FulfillmentAdapter
{
    public function getRequestInfo(Order $order): array;
}