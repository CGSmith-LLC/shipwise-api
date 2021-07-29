<?php


namespace common\services\ecommerce;


abstract class BaseEcommerceService extends \yii\base\BaseObject implements ECommerceService
{

    public abstract function applyMeta(array $metadata);

    public abstract function getOrders(): array;
}