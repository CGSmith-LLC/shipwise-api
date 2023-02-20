<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Item;
use common\models\OrderHistory;

class OrderItemAddedEvent extends Event
{
    public const EVENT_IS_ENABLED = true;
    public const EVENT_ORDER_ITEM_ADDED = 'eventOrderItemAdded';

    public Item $item;

    public static function orderItemAdded(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_ITEM_ADDED,
            'item' => $event->item
        ]);

        return $orderHistory->save();
    }
}
