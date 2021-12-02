<?php


namespace console\jobs\orders;


use common\models\Integration;
use yii\db\Exception;
use \yii\base\BaseObject;
use \yii\queue\RetryableJobInterface;

class ParseOrderJob extends BaseObject implements RetryableJobInterface
{

    /**
     * @var object $unparsedOrder
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
     * @throws Exception
     */
    public function execute($queue)
    {
        $this->integration = Integration::find()->where(['id' => $this->integration_id])->with('meta')->one();
        $adapter = $this->integration->getAdapter();
        try {
            $order = $adapter->parseOrder($this->unparsedOrder);
            // $order->save();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 5); // TODO: Return to stopping attempts
    }

    public function getTtr()
    {
        return 5;// 5 * 60; // TODO: Return to 15 minutes for production; different time better?
    }
}