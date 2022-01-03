<?php


namespace console\jobs\orders;


use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderExistsException;
use common\models\Integration;
use yii\db\Exception;
use \yii\base\BaseObject;
use \yii\queue\RetryableJobInterface;

class ParseOrderJob extends BaseObject implements RetryableJobInterface
{

    /**
     * @var $unparsedOrder
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
        try {
            $createorder = $this->integration->getService()->getFullOrderDataIfNecessary($this->unparsedOrder);
        } catch (IgnoredWebhookException $exception) {
            // this guy is just ignored and should finish out happy :)
            return true;
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
        return 5 * 60;
    }
}