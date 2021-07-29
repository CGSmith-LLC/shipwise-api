<?php


namespace common\adapters\ecommerce;


use common\models\Item;
use common\models\Order;

interface ECommerceAdapter
{
	public function parse(): Order;

	/** @return Item[] */
	public function parseItems(int $id): array;
}