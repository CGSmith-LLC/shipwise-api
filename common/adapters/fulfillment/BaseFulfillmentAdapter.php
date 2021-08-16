<?php


namespace common\adapters\fulfillment;

use common\models\Order;
use yii\base\Component;

abstract class BaseFulfillmentAdapter extends Component implements FulfillmentAdapter
{
	public const EVENT_CARTONIZATION = 'cartonizationEvent'; // TODO: Attach CartonizationEvent::handleEvent to this event

    public abstract function getCreateOrderRequestInfo(Order $order): array;
}