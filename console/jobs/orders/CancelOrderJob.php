<?php

namespace console\jobs\orders;

use common\models\Order;
use common\models\Status;
use console\jobs\NotificationJob;

class CancelOrderJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    /**
     * @var string $customer_reference
     */
    public string $customer_reference;

    /**
     * @var int $customer_id
     */
    public int $customer_id;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute($queue)
    {
        $orders = Order::find()
            ->where(['customer_reference' => $this->customer_reference])
            ->andWhere(['customer_id' => $this->customer_id])
            ->all();

        foreach ($orders as $order) {
            try {
                if ($order->status_id == Status::OPEN) {
                    $order->status_id = Status::CANCELLED;
                    $order->save();
                } else {
                    \Yii::$app->queue->push(new NotificationJob([
                        'customer_id' => $this->customer_id,
                        'subject' => '⚠️ Problem cancelling order ' . $order->customer_reference,
                        'message' => 'This order may already be at the fulfillment center or processed and might require manual intervention.',
                        'url' =>  ['/order/view', 'id' => $order->id],
                        'urlText' => 'View Order',
                    ]));
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function canRetry($attempt, $error)
    {
        return ($attempt < 5);
    }

    /**
     * @inheritDoc
     */
    public function getTtr()
    {
        return 5 * 60;
    }
}