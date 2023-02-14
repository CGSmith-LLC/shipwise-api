<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderStatusChangedEvent extends Event
{
    public const EVENT_IS_ENABLED = true;
    public const EVENT_ORDER_STATUS_CHANGED = 'eventOrderStatusChanged';

    public Order $order;
    public int $oldStatusId;

    public static function orderStatusChanged(Event $event): bool
    {
        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_STATUS_CHANGED,
            'order' => $event->order,
            'previousStatusId' => $event->oldStatusId
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
