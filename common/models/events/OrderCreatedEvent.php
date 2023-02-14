<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderCreatedEvent extends Event
{
    public const EVENT_IS_ENABLED = true;
    public const EVENT_ORDER_CREATED = 'eventOrderCreated';

    public Order $order;

    public static function orderCreated(Event $event): bool
    {
        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_CREATED,
            'order' => $event->order
        ]);

        return $orderHistory->save();
    }

    public static function trigger($class, $name, $event = null): bool
    {
        if (self::EVENT_IS_ENABLED) {
            parent::trigger($class, $name, $event);
        }

        return false;
    }
}
