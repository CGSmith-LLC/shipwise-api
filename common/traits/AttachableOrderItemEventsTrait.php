<?php

namespace common\traits;

use common\models\events\{OrderItemAddedEvent,
    OrderItemChangedEvent,
    OrderItemDeletedEvent};

trait AttachableOrderItemEventsTrait
{
    protected function attachEvents(): void
    {
        $this->on(OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED, [
            'common\models\events\OrderItemAddedEvent',
            'orderItemAdded'
        ]);

        $this->on(OrderItemChangedEvent::EVENT_ORDER_ITEM_CHANGED, [
            'common\models\events\OrderItemChangedEvent',
            'orderItemChanged'
        ]);

        $this->on(OrderItemDeletedEvent::EVENT_ORDER_ITEM_DELETED, [
            'common\models\events\OrderItemDeletedEvent',
            'orderItemDeleted'
        ]);

        $this->on(self::EVENT_AFTER_INSERT, function () {
            $this->trigger(
                OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED,
                new OrderItemAddedEvent([
                    'item' => $this
                ])
            );
        });

        $this->on(self::EVENT_BEFORE_UPDATE, function () {
            $changedAttributes = OrderItemChangedEvent::getChangedAttributes($this->dirtyAttributes, $this->oldAttributes);

            if ($changedAttributes) {
                $this->trigger(
                    OrderItemChangedEvent::EVENT_ORDER_ITEM_CHANGED,
                    new OrderItemChangedEvent([
                        'item' => $this,
                        'changedAttributes' => $changedAttributes
                    ])
                );
            }
        });

        $this->on(self::EVENT_AFTER_DELETE, function () {
            $this->trigger(
                OrderItemDeletedEvent::EVENT_ORDER_ITEM_DELETED,
                new OrderItemDeletedEvent([
                    'item' => $this
                ])
            );
        });
    }
}
