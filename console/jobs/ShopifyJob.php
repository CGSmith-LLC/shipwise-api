<?php


namespace console\jobs;


use yii\console\Exception;
use yii\queue\Queue;

class ShopifyJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $headers;
    public $body;
    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        var_dump($this->headers, $this->body);
    }
}