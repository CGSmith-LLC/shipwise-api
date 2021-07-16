<?php


namespace console\jobs\orders;


use common\adapters\ShopifyAdapter;
use common\models\Order;
use yii\console\Exception;
use yii\queue\Queue;
use \yii\base\BaseObject;
use \yii\queue\JobInterface;

class ParseOrderJob extends BaseObject implements JobInterface
{

    /**
     * @var string $order
     */
    public string $order;

    /**
     * @var string $adapter
     */
    public string $adapter;

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute($queue)
    {
        // TODO: Handle queue-switching logic when more queues are added --> All Order Jobs

        // TODO: Use 'Strategy' design pattern -> How?
        switch ($this->adapter) {
            case "shopify":
                $order = ShopifyAdapter::parse($this->order);
                break;
            default:
                throw new Exception("Adapter not valid.");
        }

        /**
         * 2. Add order to db
         */

        \Yii::$app->queue->push(new SendTo3PLJob(['orderId' => $order->id]));

    }
}