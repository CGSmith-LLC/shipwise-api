<?php

namespace console\jobs;

use common\models\{BulkItem, Order, Status};
use Exception;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
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

        // Find bulk item
        if (($bulkItem = BulkItem::findOne($this->bulkItemId)) === null) {
            throw new Exception("Bulk item not found for ID {$this->bulkItemId}");
        }

        // Create Shipping Label, update order tracking, save label as base64 data
        try {
            $shipment = $order->createShipment();
            if ($order->hasErrors()) {
                $bulkItem->errors = Json::encode($order->getErrors());
                $bulkItem->status = BulkItem::STATUS_ERROR;
            } else {
                $order->tracking  = $shipment->getMasterTracking();
                $order->status_id = Status::SHIPPED;
                $order->save(false);

                $bulkItem->base64_filedata = $shipment->mergedLabelsData;
                $bulkItem->base64_filetype = $shipment->mergedLabelsFormat;
                $bulkItem->status          = BulkItem::STATUS_DONE;
            }
        } catch (Exception $ex) {
            Yii::error($ex);
            $bulkItem->status = BulkItem::STATUS_ERROR;
            $bulkItem->errors = Json::encode($ex->getMessage());
        } finally {
            $bulkItem->save(false);
        }
    }
}
