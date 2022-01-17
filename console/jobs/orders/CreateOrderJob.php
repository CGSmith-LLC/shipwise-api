<?php

namespace console\jobs\orders;

use common\exceptions\OrderExistsException;
use common\models\Integration;
use console\jobs\NotifierJob;

class CreateOrderJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    /**
     * @var array $unparsedOrder
     */
    public $unparsedOrder;

    /**
     * @var Integration $integration
     */
    protected $integration;

    /**
     * @var int $integration_id
     */
    public int $integration_id;

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute($queue)
    {
        $this->integration = Integration::find()->where(['id' => $this->integration_id])->with('meta')->one();
        try {
            $adapter = $this->integration->getAdapter();
            $adapter->customer_id = $this->integration->customer_id;
            $order = $adapter->parseOrder($this->unparsedOrder);
            $order->save();
        } catch (OrderExistsException $e) {
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function canRetry($attempt, $error)
    {
        if ($attempt < 5) {
            return true;
        }else {
            return \Yii::$app->queue->push(new NotifierJob([
                'message' => 'Order failed to create! Our team has been notified of the issue.',
                'customer_id' => $this->integration->customer_id,
                'reason_general' => 'an order',
            ]));
        }
    }

    /**
     * @inheritDoc
     */
    public function getTtr()
    {
        return 5 * 60;
    }
}