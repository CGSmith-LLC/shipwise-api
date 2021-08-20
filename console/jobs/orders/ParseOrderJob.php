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
     * @var string $integration_name
     */
    public string $integration_name;

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
        $integration = Integration::findone(condition: ["name" => $this->integration_name]);
        $adapter = $integration->getAdapter(json: $this->order, customer_id: $this->customer_id);

        $transaction = \Yii::$app->db->beginTransaction();

        $object = $adapter->parse();

        if (!$object->save(true)) {
            $transaction->rollBack();
            throw new Exception(message: "Order could not be saved" . PHP_EOL . implode(array: $object->getErrorSummary(true), separator:PHP_EOL));
        }

        try {
            $parsedItems = $adapter->parseItems(id: $object->id);

            /** @var Item[] $parsedItems */
            foreach ($parsedItems as $parsedItem) {
                if (!$parsedItem->save(true)) {
                    throw new Exception(message: 'Could not save item.' . PHP_EOL . implode(array: $parsedItem->getErrorSummary(showAllErrors: true), separator: PHP_EOL));
                }
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new Exception($e);
        }

        \Yii::$app->queue->push(new SendTo3PLJob(['order_id' => $object->id, 'fulfillment_name' => $integration->fulfillment]));

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