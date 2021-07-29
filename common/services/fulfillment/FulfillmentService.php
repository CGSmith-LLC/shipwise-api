<?php


namespace common\services\fulfillment;


use common\models\shopify\FulfillmentMeta;

interface FulfillmentService
{
	/** @param FulfillmentMeta[] $metadata */
	public function applyMeta(array $metadata);
}