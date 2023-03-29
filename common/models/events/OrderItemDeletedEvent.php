<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Item;
use common\models\OrderHistory;

class OrderItemDeletedEvent extends Event
{
    final public const EVENT_IS_ENABLED = true;
    final public const EVENT_ORDER_ITEM_DELETED = 'eventOrderItemDeleted';

    public Item $item;

    public static function orderItemDeleted(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_ITEM_DELETED,
            'item' => $event->item
        ]);

        return $orderHistory->save();
    }
}
