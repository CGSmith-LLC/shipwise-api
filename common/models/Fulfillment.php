<?php


namespace common\models;


use common\models\shopify\FulfillmentMeta;
use common\services\fulfillment\BaseFulfillmentService;

class Fulfillment extends base\BaseFulfillment
{
    public function getAdapter()
    {
		$adaptername = "\\common\\adapters\\fulfillment\\" . $this->name . "Adapter";
		return new $adaptername();
    }

    /**
     */
    public function getService()
    {
		$serviceName = "\\common\\adapters\\fulfillment\\" . $this->name . "Service";

		/** @var BaseFulfillmentService $service */
		$service = new $serviceName();
		$service->applyMeta(FulfillmentMeta::findAll(['integration_id' => $this->id]));

		return $service;
    }
}