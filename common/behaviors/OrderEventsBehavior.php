<?php

namespace common\behaviors;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use common\models\events\OrderAddressCreatedEvent;
use common\models\events\OrderChangedEvent;
use common\models\events\OrderCreatedEvent;
use common\models\events\OrderStatusChangedEvent;
use common\models\events\OrderViewedEvent;

class OrderEventsBehavior extends Behavior
{
    public function events(): array
    {
        return [
            OrderViewedEvent::EVENT_ORDER_VIEWED => [
                'common\models\events\OrderViewedEvent',
                'orderViewed'
            ],
            OrderStatusChangedEvent::EVENT_ORDER_STATUS_CHANGED => [
                'common\models\events\OrderStatusChangedEvent',
                'orderStatusChanged'
            ],
            OrderCreatedEvent::EVENT_ORDER_CREATED => [
                'common\models\events\OrderCreatedEvent',
                'orderCreated'
            ],
            OrderAddressCreatedEvent::EVENT_ORDER_ADDRESS_CREATED => [
                'common\models\events\OrderAddressCreatedEvent',
                'orderAddressCreated'
            ],
            OrderChangedEvent::EVENT_ORDER_CHANGED => [
                'common\models\events\OrderChangedEvent',
                'orderChanged'
            ],
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventBeforeUpdate',
        ];
    }

    public function eventAfterInsert(Event $event)
    {
        $this->owner->trigger(
            OrderCreatedEvent::EVENT_ORDER_CREATED,
            new OrderCreatedEvent([
                'order' => $this->owner
            ])
        );
    }

    public function eventBeforeUpdate(Event $event)
    {
        $changedAttributes = OrderChangedEvent::getChangedAttributes($this->owner->dirtyAttributes, $this->owner->oldAttributes);

        if ($changedAttributes) {
            $this->owner->trigger(
                OrderChangedEvent::EVENT_ORDER_CHANGED,
                new OrderChangedEvent([
                    'order' => $this->owner,
                    'changedAttributes' => $changedAttributes
                ])
            );
        }

        if ($this->owner->status_id != $this->owner->getOldAttribute('status_id')) {
            $this->owner->trigger(
                OrderStatusChangedEvent::EVENT_ORDER_STATUS_CHANGED,
                new OrderStatusChangedEvent([
                    'order' => $this->owner,
                    'oldStatusId' => (int)$this->owner->getOldAttribute('status_id')
                ])
            );
        }

        if ($this->owner->address_id != $this->owner->getOldAttribute('address_id')) {
            $this->owner->trigger(
                OrderAddressCreatedEvent::EVENT_ORDER_ADDRESS_CREATED,
                new OrderAddressCreatedEvent([
                    'order' => $this->owner
                ])
            );
        }
    }
}
