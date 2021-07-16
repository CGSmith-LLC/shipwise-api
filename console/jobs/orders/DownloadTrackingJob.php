<?php


namespace console\jobs\orders;


use common\components\CustomerSettings;
use common\components\FulfillmentService;
use common\models\Order;
use common\models\Status;
use yii\console\Exception;
use yii\queue\RetryableJobInterface;
use \yii\base\BaseObject;

class DownloadTrackingJob extends BaseObject implements RetryableJobInterface
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
        if (($order = Order::findOne($this->orderId)) === null) {
            throw new Exception("Order not found for ID {$this->orderId}");
        }

        // Find upstream service to ship order to
        /** @var FulfillmentService $service */
        $name = CustomerSettings::get('fulfillment_api', $order->customer_id);
        var_dump($name);
        $service = \Yii::$app->get($name);
        $service->init();

        if ($tracking = $service->getTracking()) {
            $order->tracking = $tracking;
            $order->save();
        }

        /** TODO: Create functioning conditional
         *  if (order shipped from 3PL) {
         *      $order->status_id = Status::SHIPPED;
         *      \Yii::$app->queue->push(new UploadTrackingJob(['orderId' => $this->orderId]));
         *  }
         */
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
        return ($attempt < 5); // temporary exception? && ($error instanceof TemporaryException);
    }
}