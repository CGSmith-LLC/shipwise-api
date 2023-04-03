<?php

namespace common\models\events;

use yii\base\Event;
use common\models\Order;
use common\models\OrderHistory;

class OrderChangedEvent extends Event
{
    final public const EVENT_IS_ENABLED = true;
    final public const EVENT_ORDER_CHANGED = 'eventOrderChanged';

    public Order $order;
    public array $changedAttributes;

    public static function orderChanged(Event $event): bool
    {
        if (!self::EVENT_IS_ENABLED) {
            return false;
        }

        $orderHistory = new OrderHistory([
            'scenario' => OrderHistory::SCENARIO_ORDER_CHANGED,
            'order' => $event->order,
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

        // We need this because of different date formats:
        if (isset($changedAttributes['requested_ship_date'])) {
            $previous = $changedAttributes['requested_ship_date']['previous'];

            if ($previous == (new \DateTime($changedAttributes['requested_ship_date']['new']))->format('Y-m-d H:i:s')) {
                unset($changedAttributes['requested_ship_date']);
            }
        }

        if (isset($changedAttributes['must_arrive_by_date'])) {
            $previous = $changedAttributes['must_arrive_by_date']['previous'];

            if ($previous == (new \DateTime($changedAttributes['must_arrive_by_date']['new']))->format('Y-m-d H:i:s')) {
                unset($changedAttributes['must_arrive_by_date']);
            }
        }

        // For status we have a separate event:
        unset($changedAttributes['status_id']);

        // For address we have a separate event:
        unset($changedAttributes['address_id']);

        return $changedAttributes;
    }
}
