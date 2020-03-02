<?php

namespace console\jobs;

use common\models\{BulkItem, Order};
use common\pdf\OrderPackingSlip;
use Exception;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\JobInterface;

/**
 * Class GeneratePackingSlipJob
 *
 * This Queue Job generates a Packing Slip PDF for a given order, then stores it as base64 data.
 *
 * @version 2020.02.20
 * @package console\jobs
 */
class GeneratePackingSlipJob extends BaseObject implements JobInterface
{

    /** @var int Order ID */
    public $orderId;

    /** @var int Bulk Action Item ID */
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

        // Generate a Packing Slip and store it as base64 data
        try {
            $pdf = new OrderPackingSlip();
            $pdf->generate($order);
            $bulkItem->base64_filedata = base64_encode($pdf->Output('S'));
            $bulkItem->base64_filetype = 'PDF';
            $bulkItem->status          = BulkItem::STATUS_DONE;

        } catch (Exception $ex) {
            Yii::error($ex);
            $bulkItem->status = BulkItem::STATUS_ERROR;
            $bulkItem->errors = Json::encode($ex->getMessage());
        } finally {
            $bulkItem->save(false);
        }
    }
}
