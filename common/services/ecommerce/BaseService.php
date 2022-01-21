<?php


namespace common\services\ecommerce;


use common\models\Integration;

abstract class BaseService extends \yii\base\BaseObject
{
    public string|null $last_success_run = null;
    public int $page = 1;
    public int $perPage = 100;
    public Integration $integration;

    public abstract function applyMeta(array $metadata);

}