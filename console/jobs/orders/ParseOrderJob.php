<?php


namespace console\jobs\orders;


use common\models\Integration;
use yii\console\Exception;
use \yii\base\BaseObject;
use \yii\queue\RetryableJobInterface;

class ParseOrderJob extends BaseObject implements RetryableJobInterface
{

    /**
     * @var string $order
     */
    public string $order;

    /**
     * @var string $ecommerceSite
     */
    public string $ecommerceSite;

    /**
     * @var int $customer_id
     */
    public string $customer_id;

    /**
     * @inheritDoc
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function execute($queue)
    {
        // TODO: Handle queue-switching logic when more queues are added --> All Order Jobs
        $adapter = Integration::findone(["name" => $this->ecommerceSite])->getAdapter($this->order, $this->customer_id);

        $transaction = \Yii::$app->db->beginTransaction();

        if (!$adapter->parse()->save(true)) {
            $transaction->rollBack();
            throw new Exception("Order could not be saved");
        }

        if(!$adapter->parseItems())
        {
            $transaction->rollBack();
            throw new Exception("Items could not be saved");
        }

        $transaction->commit();

        \Yii::$app->queue->push(new SendTo3PLJob(['orderId' => $adapter->id]));

    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5);
    }

    public function getTtr()
    {
        return 5;//*/15 * 60;TODO: Return to 15 minutes for production; different time better?
    }
}