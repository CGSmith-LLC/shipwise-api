<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderAddressCreatedEvent extends Event
{
    final public const EVENT_IS_ENABLED = true;
    final public const EVENT_ORDER_ADDRESS_CREATED = 'eventOrderAddressCreated';

    public Order $order;

    public static function orderAddressCreated(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_ADDRESS_CREATED,
            'order' => $event->order
        ]);

        return $orderHistory->save();
    }
}
