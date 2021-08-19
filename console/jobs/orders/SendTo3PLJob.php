<?php


namespace console\jobs\orders;


use common\models\Order;
use yii\console\Exception;
use yii\helpers\Json;
use yii\queue\RetryableJobInterface;
use \yii\base\BaseObject;
use common\models\Fulfillment;

class SendTo3PLJob extends BaseObject implements RetryableJobInterface
{
    /** @var int Order ID */
    public int $order_id;

    /** @var string Name of Fulfillment */
    public string $fulfillment_name;

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
    public function execute($queue)
    {
		/**
		 * Find order
		 * @var Order $order
		 */

		if (($order = Order::findOne($this->order_id)) === null) {
			throw new Exception(message: "Order not found for ID {$this->order_id}");
		}

		$fulfillment = Fulfillment::findOne(['name' => $this->fulfillment_name]);
		$adapter = $fulfillment->getAdapter();
		$service = $fulfillment->getService();

		echo Json::encode($adapter->getCreateOrderRequestInfo(order: $order)) . PHP_EOL . strlen(Json::encode($adapter->getCreateOrderRequestInfo(order: $order))) . PHP_EOL;

		$service->makeCreateOrderRequest(requestInfo: $adapter->getCreateOrderRequestInfo(order: $order));

		throw new Exception(message: 'The job dies');
		\Yii::$app->queue->push(new UploadTrackingJob());
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