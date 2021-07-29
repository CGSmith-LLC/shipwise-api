<?php


namespace common\adapters\fulfillment;

use common\models\Order;
use yii\base\BaseObject;

abstract class BaseFulfillmentAdapter extends BaseObject implements FulfillmentAdapter
{
	public const EVENT_CARTONIZATION = 'cartonizationEvent'; // TODO: Attach CartonizationEvent::handleEvent to this event

    public abstract function getRequestInfo(Order $order): array;
}