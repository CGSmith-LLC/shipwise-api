<?php

namespace common\models;

use common\models\base\BaseItem;
use common\behaviors\OrderItemEventsBehavior;

/**
 * Class Item
 *
 * @package common\models
 */
class Item extends BaseItem
{
    public function behaviors(): array
    {
        return [
            [
                'class' => OrderItemEventsBehavior::class,
            ],
        ];
    }
}
