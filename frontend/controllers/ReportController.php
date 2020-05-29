<?php

namespace frontend\controllers;


use api\modules\v1\models\order\OrderEx;
use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use frontend\models\Item;
use frontend\models\Order;
use Yii;
use yii\web\Controller;

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
     * @inheritDoc
     */
    public function actionIndex()
    {
        $model = new ReportForm();

        // Generate Report
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            // @todo add customer level validation for the user before continuing

            $model->start_date = Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d');
            $model->end_date   = Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d');

            $orders = Order::find()
                ->where(['customer_id' => $model->customers])
                ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
                ->orderBy('created_date')
                ->all();


            return $this->generateCsv($orders);
        }


        return $this->render('index', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
        ]);
    }

    public function generateCsv($orders)
    {
        //csv header
        $order = new Order();
        $item = new Item();
        $body = [
            [
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
            ]
        ];

        //csv body
        /** @var Order $order */
        foreach ($orders as $order) {
            foreach ($order->getItems()->all() as $item) {
                $body[] = [
                    $order->customer_reference,
                    $order->created_date,
                    $order->id,
                    $order->order_reference,
                    $order->status->name,
                    $order->address->name,
                    $order->address->city,
                    $order->address->state->name,
                    $order->address->zip,
                    $order->address->notes,
                    $order->notes,
                    $order->tracking,
                    $order->carrier->name,
                    $order->service->name,
                    $order->requested_ship_date,
                    $order->po_number,
                    $order->origin,
                    $item->quantity,
                    $item->sku,
                    $item->name,
                ];
            }
        }

        $fp = fopen('file.csv', 'w');
        foreach ($body as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        $output = file_get_contents('file.csv');

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="shipwise-report-' . date('YmdHi') .'.csv"');

        echo $output;
        die;
    }

}