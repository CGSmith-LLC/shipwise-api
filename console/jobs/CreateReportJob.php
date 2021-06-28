<?php

namespace console\jobs;

use common\models\Item;
use common\models\Order;
use common\models\Package;
use common\models\PackageItem;
use frontend\models\User;
use phpDocumentor\Reflection\Types\This;
use Yii;
use yii\helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use common\models\Customer;

class CreateReportJob extends BaseObject implements JobInterface
{
    /**
     * @var int $customer
     */
    public int $customer;

    /**
     * @var int $userId
     */
    public int $user_id;

    /**
     * @var string $start_date
     */
    public string $start_date;

    /**
     * @var string $end_date
     */
    public string $end_date;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $ordersQuery = \frontend\models\Order::find()
            ->where(['customer_id' => $this->customer])
            ->andWhere(['between', 'created_date', $this->start_date, $this->end_date])
            ->with(
                [
                    'items',
                    'status',
                    'carrier',
                    'service',
                    'packages',
                    'address.state',
                ]
            )
            ->orderBy('created_date');

        $dir      = Yii::getAlias('@frontend') . '/runtime/reports/';
        $filename = $this->user_id . "_report.csv";

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

        Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo(User::findone(['id' => $this->user_id])->email)
            ->setSubject('Generated Report for ' . $this->start_date . ' to ' . $this->end_date)
            ->setTextBody(
                'Here is your requested CSV Order Report for ' . Customer::findone(['id' => $this->customer])->name .
                ' from ' . Yii::$app->formatter->asDate($this->start_date, 'php:l, F j, Y') . ' to ' .
                Yii::$app->formatter->asDate($this->end_date, 'php:l, F j, Y') . '.'
            )
            ->attach($dir . $filename, [
                'shipwise-report-' . date('YmdHi') . '.csv',
                [
                    'mimeType' => 'text/csv',
                ],
            ])
            ->send();

        /*return Yii::$app->response->sendFile(
            $dir . $filename,
            'shipwise-report-' . date('YmdHi') . '.csv',
            [
                'mimeType' => 'text/csv',
            ]
        );*/
    }
}