<?php


namespace common\services\fulfillment;


use common\models\FulfillmentMeta;

interface FulfillmentService
{
	/** @param FulfillmentMeta[] $metadata */
	public function applyMeta(array $metadata);

	/**
	 * @return bool if succeeded
	 */
	public function makeCreateOrderRequest(array $requestInfo): bool;
}