<?php

namespace console\jobs;

use common\models\Item;
use common\models\Order;
use common\models\Package;
use common\models\PackageItem;
use Yii;
use Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class CreateReportJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{

    /**
     * @var ActiveRecord $ordersQueryId
     */
    public $ordersQueryId;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        if(($ordersQuery = ActiveRecord::findOne($this->ordersQueryId)) === null)
        {
            throw new Exception("ActiveQuery not found for ID {$this->ordersQueryId}");
        }

        $dir      = Yii::getAlias('@frontend') . '/runtime/';
        $filename = Yii::$app->user->id . "_report.csv";

        // csv header
        $order  = new Order();
        $item   = new Item();
        $header = [
            $order->getAttributeLabel('customer_reference'),
            $order->getAttributeLabel('created_date'),
            $order->getAttributeLabel('id'),
            $order->getAttributeLabel('order_reference'),
            $order->getAttributeLabel('status.name'),
            $order->getAttributeLabel('address.name'),
            $order->getAttributeLabel('address.city'),
            'State',
            $order->getAttributeLabel('address.zip'),
            $order->getAttributeLabel('address.notes'),
            $order->getAttributeLabel('notes'),
            $order->getAttributeLabel('tracking'),
            $order->getAttributeLabel('carrier.name'),
            $order->getAttributeLabel('service.name'),
            $order->getAttributeLabel('requested_ship_date'),
            $order->getAttributeLabel('po_number'),
            $order->getAttributeLabel('origin'),
            $item->getAttributeLabel('quantity'),
            $item->getAttributeLabel('sku'),
            $item->getAttributeLabel('name'),
            $item->getAttributeLabel('lot_number'),
        ];

        $fp = fopen($dir . $filename, 'w');

        // csv header row
        fputcsv($fp, $header);
        // csv body
        foreach ($ordersQuery->batch(500) as $orders) {
            foreach ($orders as $order) {

                // @todo no foreach? cleanup code? fine for now? ship toinght? love me long time. oh god my existence is
                /**
                 * @var Package $package
                 */
                $packageId = [];
                foreach ($order->packages as $package) {
                    foreach ($package->items as $packageItems) {
                        $packageId[] = $packageItems->id;
                    }
                }

                foreach ($order->items as $item) {
                    /**
                     * Find lot number
                     */
                    $lotNumbers = PackageItem::find()
                        ->with('lotInfo')
                        ->where(['in', 'id', $packageId])
                        ->andWhere(['sku' => $item->sku])
                        ->all();
                    $lotNumber = ArrayHelper::getColumn($lotNumbers, function($packageItem) {
                        $lotNumbers = [];
                        /** @var PackageItem $packageItem */
                        foreach ($packageItem->lotInfo as $lot) {
                            $lotNumbers[] = $lot->lot_number;
                        }
                        return implode(' ', $lotNumbers);
                    });
                    $lotNumber = implode(' ', $lotNumber);

                    // Output csv
                    fputcsv(
                        $fp,
                        [
                            $order->customer_reference,
                            $order->created_date,
                            $order->id,
                            $order->order_reference,
                            $order->status->name,
                            $order->address->name ?? null,
                            $order->address->city ?? null,
                            $order->address->state->name ?? null,
                            $order->address->zip ?? null,
                            $order->address->notes ?? null,
                            $order->notes,
                            $order->tracking,
                            $order->carrier->name ?? null,
                            $order->service->name ?? null,
                            $order->requested_ship_date,
                            $order->po_number,
                            $order->origin,
                            $item->quantity,
                            $item->sku,
                            $item->name,
                            $lotNumber,
                        ]
                    );
                }
            }
        }
        fclose($fp);

        return Yii::$app->response->sendFile(
            $dir . $filename,
            'shipwise-report-' . date('YmdHi') . '.csv',
            [
                'mimeType' => 'text/csv',
            ]
        );
    }
}