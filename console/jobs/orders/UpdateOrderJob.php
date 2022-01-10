<?php

namespace console\jobs\orders;

use common\exceptions\OrderExistsException;
use common\models\Integration;
use common\models\Status;

class UpdateOrderJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    /**
     * @var int $status Integer of Status in Shipwise's DB
     * @see Status
     */
    public int $status;

    /**
     * @var string $customer_reference Customer Reference number to change status
     */
    public string $customer_reference;

    /**
     * @var int $integration_id Integration ID
     */
    public int $integration_id;

    /**
     * @var Integration $integration Integration to call. This is used by the service
     */
    protected $integration;


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