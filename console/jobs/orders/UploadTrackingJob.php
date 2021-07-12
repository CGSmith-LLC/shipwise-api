<?php


namespace console\jobs\orders;


use common\models\Order;
use common\models\Status;
use yii\queue\Queue;
use yii\console\Exception;
use \yii\base\BaseObject;
use \yii\queue\JobInterface;

class UploadTrackingJob extends BaseObject implements JobInterface
{

    /**
     * @var int $orderId
     */
    public int $orderId;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        if (($order = Order::findOne($this->orderId)) === null) {
            throw new Exception("Order not found for ID {$this->orderId}");
        }

        /** TODO: Make Function
         * 1. Get Origin from Order
         * 2. Mark order as shipped in Origin
         * $order->status_id = Status::COMPLETE;
         */
    }
}