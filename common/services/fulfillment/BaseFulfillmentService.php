<?php

namespace common\services\fulfillment;

use yii\base\BaseObject;

abstract class BaseFulfillmentService extends BaseObject implements FulfillmentService
{
	public abstract function applyMeta(array $metadata);

	public abstract function makeCreateOrderRequest(array $requestInfo): bool;
}