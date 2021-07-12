<?php

namespace common\adapters;

use \yii\base\BaseObject;
use \common\models\Order;

class ShopifyAdapter extends BaseObject
{

    public static function parse($orderJSON): Order
    {
        /** TODO: Make Function
         * 1. Parse JSON to array/object
         * 2. Create ShipWise Order object & fill in with JSON details
         * 3. Return Order object
         */
    }

}