<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderViewedEvent extends Event
{
    public const EVENT_IS_ENABLED = false;
    public const EVENT_ORDER_VIEWED = 'eventOrderViewed';

    public Order $order;

    public static function orderViewed(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_VIEWED,
            'order' => $event->order,
        ]);

        return $orderHistory->save();
    }
}
