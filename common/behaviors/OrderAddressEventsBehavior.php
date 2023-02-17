<?php

namespace common\behaviors;

use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;
use common\models\Order;
use common\models\events\OrderAddressChangedEvent;

class OrderAddressEventsBehavior extends Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'eventBeforeUpdate',
            OrderAddressChangedEvent::EVENT_ORDER_ADDRESS_CHANGED => [
                'common\models\events\OrderAddressChangedEvent',
                'orderAddressChanged'
            ],
        ];
    }

    public function eventBeforeUpdate(Event $event)
    {
        // We need to use `ORDER BY id DESC` since we used to have one
        // address for several different orders:
        $order = Order::find()
            ->where(['address_id' => $this->owner->id])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        if ($order) {
            $changedAttributes = OrderAddressChangedEvent::getChangedAttributes($this->owner->dirtyAttributes, $this->owner->oldAttributes);

            if ($changedAttributes) {
                $this->owner->trigger(
                    OrderAddressChangedEvent::EVENT_ORDER_ADDRESS_CHANGED,
                    new OrderAddressChangedEvent([
                        'address' => $this->owner,
                        'order' => $order,
                        'changedAttributes' => $changedAttributes
                    ])
                );
            }
        }
    }
}
