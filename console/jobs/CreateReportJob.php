<?php

namespace console\jobs;

use Yii;
use bilberrry\spaces\Service;
use common\models\Item;
use common\models\Order;
use common\models\Package;
use common\models\PackageItem;
use yii\helpers\ArrayHelper;
use yii\base\BaseObject;
use common\models\Customer;
use yii\helpers\FileHelper;
use yii\queue\RetryableJobInterface;

class CreateReportJob extends BaseObject implements RetryableJobInterface
{
    const MAX_ATTEMPTS = 3;
    const TTR = 600; // in seconds

    /**
     * @var int $customer
     */
    public int $customer;

    /**
     * @var int $userId
     */
    public int $user_id;

    /**
     * @var string $user_email
     */
    public string $user_email;

    /**
     * @var string $start_date
     */
    public string $start_date;

    /**
     * @var string $end_date
     */
    public string $end_date;

    /**
     * @var bool $items if true then include items in report
     */
    public bool $items;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $reportPath = $this->processReport();
        $url = $this->uploadToStorage($reportPath);
        $this->sendReportEmail($url);
    }

    public function getTtr(): int
    {
        return self::TTR;
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < self::MAX_ATTEMPTS);
    }

    public function isFailed(int $currentAttempt): int
    {
        return $currentAttempt === self::MAX_ATTEMPTS;
    }

    protected function processReport(): string
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

        $dir = Yii::getAlias('@console') . '/runtime/reports/';
        FileHelper::createDirectory($dir);
        $filename = time() . '_' . $this->user_id . "_report.csv";

        // csv header
        $order = new Order();
        $item = new Item();
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
            $order->getAttributeLabel('must_arrive_by_date'),
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
                if ($this->items) {
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
                        $lotNumber = ArrayHelper::getColumn($lotNumbers, function ($packageItem) {
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
                                $order->must_arrive_by_date,
                                $order->po_number,
                                $order->origin,
                                $item->quantity,
                                $item->sku,
                                $item->name,
                                $lotNumber,
                            ]
                        );
                    }
                } else {
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
                            $order->must_arrive_by_date,
                            $order->po_number,
                            $order->origin,
                            '',
                            '',
                            '',
                            '',
                        ]
                    );
                }
            }
        }
        fclose($fp);

        // NEVER use 777 - except for here... Systemd runs as www-data but the runtime folder is root:root
        // since this uploads to storage... locally 777 is fine.
        chmod($dir . $filename, 0777);

        return $dir . $filename;
    }

    protected function uploadToStorage(string $reportPath): string
    {
        /** @var Service $storage */
        $storage = Yii::$app->get('storage');

        $date = date('YmdHi');

        $storage->upload('shipwise-report-' . $date . '.csv', $reportPath);
        $url = $storage->getUrl('shipwise-report-' . $date . '.csv');

        return $url;
    }

    protected function sendReportEmail(string $reportUrl): void
    {
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = '@frontend/views/mail';
        $mailer->getView()->theme = \Yii::$app->view->theme;

        $formatter = Yii::$app->getFormatter();
        $startDate = $formatter->asDate($this->start_date, 'php:F j, Y');
        $endDate = $formatter->asDate($this->end_date, 'php:F j, Y');
        $mailer->compose(['html' => 'report'], [
            'url' => $reportUrl,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'customerName' => Customer::findone(['id' => $this->customer])->name,
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($this->user_email)
            ->setSubject('Generated Report for ' . $startDate . ' to ' . $endDate)
            ->send();
    }

    public static function sendFailEmail(string $sendTo, int $customerId): void
    {
        $customer = Customer::findone(['id' => $customerId]);

        if ($customer) {
            Yii::$app->mailer->viewPath = '@frontend/views/mail';
            Yii::$app->mailer->getView()->theme = Yii::$app->view->theme;

            Yii::$app->mailer->compose(
                [
                    'html' => 'report-failed'
                ],
                [
                    'customer' => $customer,
                ]
            )
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo($sendTo)
            ->setSubject('Please regenerate your last report')
            ->send();
        }
    }
}
