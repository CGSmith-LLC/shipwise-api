<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderStatusChangedEvent extends Event
{
    final public const EVENT_IS_ENABLED = true;
    final public const EVENT_ORDER_STATUS_CHANGED = 'eventOrderStatusChanged';

    public Order $order;
    public int $oldStatusId;

    public static function orderStatusChanged(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_STATUS_CHANGED,
            'order' => $event->order,
            'previousStatusId' => $event->oldStatusId
        ]);

        return $orderHistory->save();
    }
}
