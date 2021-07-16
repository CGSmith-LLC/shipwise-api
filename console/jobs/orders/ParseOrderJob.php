<?php


namespace console\jobs\orders;


use common\adapters\ShopifyAdapter;
use common\models\Order;
use yii\console\Exception;
use yii\queue\Queue;
use \yii\base\BaseObject;
use \yii\queue\RetryableJobInterface;

class ParseOrderJob extends BaseObject implements RetryableJobInterface
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
     * @throws \yii\db\Exception
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

        $transaction = \Yii::$app->db->beginTransaction();

        if (!$order->save(true)) {
            $transaction->rollBack();
            throw new Exception("Order could not be saved");
        }

        $transaction->commit();

        \Yii::$app->queue->push(new SendTo3PLJob(['orderId' => $order->id]));

    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5);
    }

    public function getTtr()
    {
        return 15 * 60;
    }
}