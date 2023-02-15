<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Item;
use common\models\OrderHistory;

class OrderItemChangedEvent extends Event
{
    public const EVENT_IS_ENABLED = true;
    public const EVENT_ORDER_ITEM_CHANGED = 'eventOrderItemChanged';

    public Item $item;
    public array $changedAttributes;

    public static function orderItemChanged(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_ITEM_CHANGED,
            'item' => $event->item,
            'changedAttributes' => $event->changedAttributes
        ]);

        return $orderHistory->save();
    }

    public static function getChangedAttributes(array $dirtyAttributes, array $oldAttributes): array
    {
        $changedAttributes = [];

        foreach ($dirtyAttributes as $key => $dirtyAttribute) {
            if ($dirtyAttribute != $oldAttributes[$key]) {
                $changedAttributes[$key] = [
                    'previous' => $oldAttributes[$key],
                    'new' => $dirtyAttribute,
                ];
            }
        }

        return $changedAttributes;
    }
}
