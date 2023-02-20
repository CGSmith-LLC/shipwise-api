<?php

namespace common\behaviors;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use common\models\events\OrderItemAddedEvent;
use common\models\events\OrderItemChangedEvent;
use common\models\events\OrderItemDeletedEvent;

class OrderItemEventsBehavior extends Behavior
{
    public function events(): array
    {
        return [
            OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED => [
                'common\models\events\OrderItemAddedEvent',
                'orderItemAdded'
            ],
            OrderItemChangedEvent::EVENT_ORDER_ITEM_CHANGED => [
                'common\models\events\OrderItemChangedEvent',
                'orderItemChanged'
            ],
            OrderItemDeletedEvent::EVENT_ORDER_ITEM_DELETED => [
                'common\models\events\OrderItemDeletedEvent',
                'orderItemDeleted'
            ],
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventBeforeUpdate',
            ActiveRecord::EVENT_AFTER_DELETE => 'eventAfterDelete',
        ];
    }

    public function eventAfterInsert(Event $event)
    {
        $this->owner->trigger(
            OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED,
            new OrderItemAddedEvent([
                'item' => $this->owner
            ])
        );
    }

    public function eventBeforeUpdate(Event $event)
    {
        $changedAttributes = OrderItemChangedEvent::getChangedAttributes($this->owner->dirtyAttributes, $this->owner->oldAttributes);

        if ($changedAttributes) {
            $this->owner->trigger(
                OrderItemChangedEvent::EVENT_ORDER_ITEM_CHANGED,
                new OrderItemChangedEvent([
                    'item' => $this->owner,
                    'changedAttributes' => $changedAttributes
                ])
            );
        }
    }

    public function eventAfterDelete(Event $event)
    {
        $this->owner->trigger(
            OrderItemDeletedEvent::EVENT_ORDER_ITEM_DELETED,
            new OrderItemDeletedEvent([
                'item' => $this->owner
            ])
        );
    }
}
