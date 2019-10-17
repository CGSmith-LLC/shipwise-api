<?php

namespace frontend\models;

use common\models\Item as BaseItem;

/**
 * Class Item
 *
 * @package frontend\models
 */
class Item extends BaseItem
{
    /** {@inheritdoc} */
    public function rules()
    {
        // overwrite parent rules to remove required order_id
        return [
            [['quantity', 'sku'], 'required'],
            [['order_id', 'quantity'], 'integer'],
            ['sku', 'string', 'max' => 64],
            ['name', 'string', 'max' => 128],
        ];
    }
}