<?php


namespace common\models;


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
		$serviceName = "\\common\\services\\fulfillment\\" . $this->name . "Service";

		/** @var BaseFulfillmentService $service */
		$service = new $serviceName();
		$service->applyMeta(FulfillmentMeta::findAll(condition: ['integration_id' => $this->id]));

		return $service;
    }
}