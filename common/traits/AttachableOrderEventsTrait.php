<?php

namespace common\traits;

use common\models\events\{OrderAddressCreatedEvent,
    OrderChangedEvent,
    OrderCreatedEvent,
    OrderStatusChangedEvent,
    OrderViewedEvent};

trait AttachableOrderEventsTrait
{
    protected function attachEvents(): void
    {
        $this->on(OrderViewedEvent::EVENT_ORDER_VIEWED, [
            'common\models\events\OrderViewedEvent',
            'orderViewed'
        ]);

        $this->on(OrderStatusChangedEvent::EVENT_ORDER_STATUS_CHANGED, [
            'common\models\events\OrderStatusChangedEvent',
            'orderStatusChanged'
        ]);

        $this->on(OrderCreatedEvent::EVENT_ORDER_CREATED, [
            'common\models\events\OrderCreatedEvent',
            'orderCreated'
        ]);

        $this->on(OrderAddressCreatedEvent::EVENT_ORDER_ADDRESS_CREATED, [
            'common\models\events\OrderAddressCreatedEvent',
            'orderAddressCreated'
        ]);

        $this->on(OrderChangedEvent::EVENT_ORDER_CHANGED, [
            'common\models\events\OrderChangedEvent',
            'orderChanged'
        ]);

        $this->on(self::EVENT_BEFORE_UPDATE, function () {
            $changedAttributes = OrderChangedEvent::getChangedAttributes($this->dirtyAttributes, $this->oldAttributes);

            if ($changedAttributes) {
                $this->trigger(
                    OrderChangedEvent::EVENT_ORDER_CHANGED,
                    new OrderChangedEvent([
                        'order' => $this,
                        'changedAttributes' => $changedAttributes
                    ])
                );
            }

            if ($this->status_id != $this->getOldAttribute('status_id')) {
                $this->trigger(
                    OrderStatusChangedEvent::EVENT_ORDER_STATUS_CHANGED,
                    new OrderStatusChangedEvent([
                        'order' => $this,
                        'oldStatusId' => (int)$this->getOldAttribute('status_id')
                    ])
                );
            }

            if ($this->address_id != $this->getOldAttribute('address_id')) {
                $this->trigger(
                    OrderAddressCreatedEvent::EVENT_ORDER_ADDRESS_CREATED,
                    new OrderAddressCreatedEvent([
                        'order' => $this
                    ])
                );
            }
        });

        $this->on(self::EVENT_AFTER_INSERT, function () {
            $this->trigger(
                OrderCreatedEvent::EVENT_ORDER_CREATED,
                new OrderCreatedEvent([
                    'order' => $this
                ])
            );
        });
    }
}
