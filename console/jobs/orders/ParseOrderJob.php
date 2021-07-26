<?php


namespace console\jobs\orders;


use common\models\Integration;
use common\models\Item;
use yii\db\Exception;
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
     */
    public function execute($queue)
    {
        // TODO: Handle queue-switching logic when more queues are added --> All Order Jobs
        $adapter = Integration::findone(["name" => $this->ecommerceSite])->getAdapter($this->order, $this->customer_id);

        $transaction = \Yii::$app->db->beginTransaction();

        $object = $adapter->parse();

        if (!$object->save(true)) {
            $transaction->rollBack();
            throw new Exception("Order could not be saved" . PHP_EOL . implode(PHP_EOL, $object->getErrorSummary(true)));
        }

        try {
            $parsedItems = $adapter->parseItems($object->id);

            /** @var Item[] $parsedItems */
            foreach ($parsedItems as $parsedItem) {
                if (!$parsedItem->save()) {
                    throw new Exception('Could not save item.' . PHP_EOL . implode(PHP_EOL, $parsedItem->getErrorSummary(true)));
                }
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new Exception($e);
        }

        \Yii::$app->queue->push(new SendTo3PLJob(['orderId' => $object->id]));

    }

    public function canRetry($attempt, $error)
    {
        return true;//($attempt < 5); TODO: Return to stopping attempts
    }

    public function getTtr()
    {
        return 5;//15 * 60;TODO: Return to 15 minutes for production; different time better?
    }
}