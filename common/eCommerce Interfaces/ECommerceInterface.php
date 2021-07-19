<?php

namespace common\interfaces;

abstract class ECommerceInterface extends \yii\base\BaseObject
{

    public abstract function getOrders();

}