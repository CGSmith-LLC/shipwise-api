<?php

namespace console\jobs;

use common\models\{BulkItem, Order};
use Exception;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class CreateShippingLabelJob
 *
 * This Queue Job creates a Shipping Label for a given order.
 *
 * @version 2020.02.20
 * @package console\jobs
 */
class CreateShippingLabelJob extends BaseObject implements JobInterface
{

    /** @var int Order ID */
    public $orderId;

    /** @var int Bulk Action Item ID if any */
    public $bulkItemId = null;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute($queue)
    {
        // Find order
        if (($order = Order::findOne($this->orderId)) === null) {
            throw new Exception("Order not found for ID {$this->orderId}");
        }

        // Create shipping label
        $success = $order->createShippingLabel();

        // If this job is part of a bulk action then update the status
        if ($this->bulkItemId && (($bulkItem = BulkItem::findOne($this->bulkItemId)) !== null)) {
            $bulkItem->status = $success ? BulkItem::STATUS_DONE : BulkItem::STATUS_ERROR;
            $bulkItem->save();
        }
    }
}
