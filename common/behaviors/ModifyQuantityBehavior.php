<?php

namespace common\behaviors;

use yii\base\Event;

class ModifyQuantityBehavior extends \yii\base\Behavior
{

    public $sku;

    public function run()
    {
        $event = null;
        \Yii::debug($event->data);
    }
    
}