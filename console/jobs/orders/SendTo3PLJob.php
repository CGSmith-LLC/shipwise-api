<?php


namespace console\jobs\orders;


use common\components\ColdcoFulfillmentService;
use common\models\Order;
use yii\console\Exception;
use yii\queue\RetryableJobInterface;
use \yii\base\BaseObject;

class SendTo3PLJob extends BaseObject implements RetryableJobInterface
{
    /** @var int Order ID */
    public int $orderId;

    /** @var int Order ID */
    public int $fulfillment_name;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {

        /**
         * Find order
         * @var Order $order
         */
        if (($order = Order::findOne($this->orderId)) === null) {
            throw new Exception("Order not found for ID {$this->orderId}");
        }

        // Find upstream service to ship order to
        /** @var ColdcoFulfillmentService $service */
        $service = \Yii::$app->get('coldco');
        //$service->fulfillmentService(CustomerSettings::getObjectByValue('fulfillment_api', $order->customer_id))->generateNewToken();

        //$service->createOrder($order);

        \Yii::$app->queue->push(new DownloadTrackingJob(['orderId' => $this->orderId]));
    }

    /**
     * @inheritDoc
     */
    public function getTtr()
    {
        return 5;//15 * 60; TODO
    }

    /**
     * @inheritDoc
     */
    public function canRetry($attempt, $error)
    {
        return true;//($attempt < 5); // temporary exception? && ($error instanceof TemporaryException); TODO
    }
}