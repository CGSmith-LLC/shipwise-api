<?php

namespace common\traits;

use common\models\Order;
use common\models\events\{OrderAddressChangedEvent};

trait AttachableOrderAddressEventsTrait
{
    protected function attachEvents(): void
    {
        $this->on(OrderAddressChangedEvent::EVENT_ORDER_ADDRESS_CHANGED, [
            'common\models\events\OrderAddressChangedEvent',
            'orderAddressChanged'
        ]);

        $this->on(self::EVENT_BEFORE_UPDATE, function () {
            $order = Order::find()
                ->where(['address_id' => $this->id])
                ->one();

            if ($order) {
                $changedAttributes = OrderAddressChangedEvent::getChangedAttributes($this->dirtyAttributes, $this->oldAttributes);

                if ($changedAttributes) {
                    $this->trigger(
                        OrderAddressChangedEvent::EVENT_ORDER_ADDRESS_CHANGED,
                        new OrderAddressChangedEvent([
                            'address' => $this,
                            'order' => $order,
                            'changedAttributes' => $changedAttributes
                        ])
                    );
                }
            }
        });
    }
}
