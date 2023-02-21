<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderCreatedEvent extends Event
{
    public const EVENT_IS_ENABLED = false;
    public const EVENT_ORDER_CREATED = 'eventOrderCreated';

    public Order $order;

    public static function orderCreated(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_CREATED,
            'order' => $event->order
        ]);

        return $orderHistory->save();
    }
}
