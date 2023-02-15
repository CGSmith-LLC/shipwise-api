<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\Address;
use common\models\OrderHistory;

class OrderAddressChangedEvent extends Event
{
    public const EVENT_IS_ENABLED = true;
    public const EVENT_ORDER_ADDRESS_CHANGED = 'eventOrderAddressChanged';

    public Order $order;
    public Address $address;
    public array $changedAttributes;

    public static array $skipAttributes = [
        'created_date', 'updated_date'
    ];

    public static function orderAddressChanged(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_ADDRESS_CHANGED,
            'order' => $event->order,
            'address' => $event->address,
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

        // Remove attributes that must be skipped:
        foreach ($changedAttributes as $key => $value) {
            if (in_array($key, self::$skipAttributes)) {
                unset($changedAttributes[$key]);
            }
        }

        return $changedAttributes;
    }
}
