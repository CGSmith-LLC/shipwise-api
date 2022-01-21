<?php

namespace console\jobs\orders;

use common\exceptions\OrderExistsException;
use common\models\Integration;
use common\models\Order;
use common\models\Status;
use console\jobs\NotifierJob;

class CancelOrderJob extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

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

        $order = Order::find()
            ->where(['customer_reference' => $this->customer_reference])
            ->andWhere(['customer_id' => $this->integration->customer_id])
            ->one();
        try {
            // If Fulfillment integration exists AND Order is PENDING cancel upstream
            if ($fulfillmentIntegrations = Integration::find()
                    ->where(['customer_id' => $this->integration->customer_id])
                    ->andWhere(['type' => Integration::TYPE_FULFILLMENT])
                    ->with('meta')
                    ->all() && $order->status_id == Status::PENDING) {
                /** @var $fulfillmentIntegration Integration **/
                foreach ($fulfillmentIntegrations as $fulfillmentIntegration) {
                    // @todo should lookup and track integration at an order level
                    $service = $fulfillmentIntegration->getService();
                    $service->cancelOrder($order);
                }
            }

            // Delete locally (mark as cancelled)
            $order->status_id = Status::CANCELLED;
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
        } else {
            \Yii::$app->queue->push(new NotifierJob([
                'message' => 'We had an issue automatically cancelling an order. Manual action may be required by you.',
                'customer_reference' => $this->customer_reference,
                'customer_id' => $this->integration->customer_id,
                'reason_general' => 'an order',
                'reason_specific' => 'Order #' . $this->customer_reference,
            ]));
            return false;
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