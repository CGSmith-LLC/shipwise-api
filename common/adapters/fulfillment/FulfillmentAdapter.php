<?php


namespace common\adapters\fulfillment;


use common\models\Order;

interface FulfillmentAdapter
{
	public function getCreateOrderRequestInfo(Order $order): array;
}