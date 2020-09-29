<?php

namespace frontend\controllers;

use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use frontend\models\Item;
use frontend\models\Order;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class ReportController
 *
 * @package frontend\controllers
 */
class ReportController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Index for creating CSV report
     *
     * @return string|void
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $model = new ReportForm();

        // Generate Report
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            $model->start_date = Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d');
            $model->end_date   = Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d');

            $ordersQuery = Order::find()
                ->where(['customer_id' => $model->customer])
                ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
                ->with(
                    [
                        'items',
                        'status',
                        'carrier',
                        'service',
                        'address' => function (ActiveQuery $query) {
                            $query->with('state');
                        },
                    ]
                )
                ->orderBy('created_date');


            return $this->generateCsv($ordersQuery);
        }


        return $this->render(
            'index',
            [
                'model'     => $model,
                'customers' => Yii::$app->user->identity->isAdmin
                    ? Customer::getList()
                    : Yii::$app->user->identity->getCustomerList(),
            ]
        );
    }

    /**
     * Generate and output CSV file for download
     *
     * @param ActiveQuery $ordersQuery
     *
     * @return \yii\console\Response|\yii\web\Response
     */
    private function generateCsv(ActiveQuery $ordersQuery)
    {
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
        ];

        $fp = fopen($dir . $filename, 'w');

        // csv header row
        fputcsv($fp, $header);

        // csv body
        foreach ($ordersQuery->batch(500) as $orders) {
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
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
