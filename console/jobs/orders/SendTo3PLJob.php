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
    public $orderId;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {

        /**
         * Find order
         * @var Order $order
         */
        if (($this->order = Order::findOne($this->orderId)) === null) {

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
        return 15 * 60;
    }

    /**
     * @inheritDoc
     */
    public function canRetry($attempt, $error)
    {
        return ($attempt < 5); // temporary execption? && ($error instanceof TemporaryException);
    }
}