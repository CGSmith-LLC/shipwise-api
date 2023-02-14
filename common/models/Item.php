<?php

namespace common\models;

use common\models\base\BaseItem;
use common\models\events\OrderItemAddedEvent;

/**
 * Class Item
 *
 * @package common\models
 */
class Item extends BaseItem
{
    public function init(): void
    {
        $this->on(OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED, [
            'common\models\events\OrderItemAddedEvent',
            'orderItemAdded'
        ]);

        $this->on(self::EVENT_AFTER_INSERT, function () {
            $this->trigger(
                OrderItemAddedEvent::EVENT_ORDER_ITEM_ADDED,
                new OrderItemAddedEvent([
                    'item' => $this
                ])
            );
        });
    }
}
