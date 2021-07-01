<?php

namespace frontend\controllers;

use common\models\Status;
use frontend\models\Customer;
use frontend\models\forms\DashboardForm;
use frontend\models\forms\ReportForm;
use frontend\models\Order;
use Yii;
use yii\base\BaseObject;
use yii\base\Response;
use yii\web\Controller;
use DateTime;
use yii\web\Request;

/**
 * Site controller
 */
class SiteController extends \frontend\controllers\Controller
{

    /**
     * {@inheritdoc}
     */
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }


    /**
     * Index for the dashboard numbers
     *
     * @inheritDoc
     */
    public function actionIndex()
    {
        $model = new DashboardForm();

        $customers = Yii::$app->user->identity->isAdmin
            ? Customer::getList()
            : Yii::$app->user->identity->getCustomerList();

        $model->setAttributes([
            'start_date' => date('Y-m-d 00:00:00'),
            'end_date' => date('Y-m-d 23:59:59'),
            'customers' => array_key_first($customers)
        ]);

        // Generate Report
        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            // @todo add customer level validation for the user before continuing

            $model->start_date = Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d 00:00:00');
            $model->end_date = Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d 23:59:59');

        }

        $open = Order::find()
            ->where(['customer_id' => $model->customers])
            ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
            ->andWhere(['status_id' => Status::OPEN])
            ->count();
        $pending = Order::find()
            ->where(['customer_id' => $model->customers])
            ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
            ->andWhere(['status_id' => Status::PENDING])
            ->count();
        $shipped = Order::find()
            ->where(['customer_id' => $model->customers])
            ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
            ->andWhere(['status_id' => Status::SHIPPED])
            ->count();
        $completed = Order::find()
            ->where(['customer_id' => $model->customers])
            ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
            ->andWhere(['status_id' => Status::COMPLETED])
            ->count();
        $error = Order::find()
            ->where(['customer_id' => $model->customers])
            ->andWhere(['between', 'created_date', $model->start_date, $model->end_date])
            ->andWhere(['status_id' => Status::WMS_ERROR])
            ->count();

        return $this->render('index', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
            'openCount' => $open,
            'pendingCount' => $pending,
            'shippedCount' => $shipped,
            'completedCount' => $completed,
            'errorCount' => $error,
        ]);
    }

    public function actionJson()
    {
        /**
         * gets default start and end dates of query
         */
        $defaultStart = new DateTime('now');
        $defaultStart->modify('-180 day');
        $defaultStart = $defaultStart->format('Y-m-d 00:00:00');
        $defaultEnd = new DateTime('now');
        $defaultEnd = $defaultEnd->format('Y-m-d 23:59:59');
        /**
         * gets inputted start and end dates of query
         */
        $request = Yii::$app->request;
        $start_date = Yii::$app->formatter->asDate($request->get('start_date', $defaultStart), 'php:Y-m-d 00:00:00');
        $end_date = Yii::$app->formatter->asDate($request->get('end_date', $defaultEnd), 'php:Y-m-d 23:59:59');

        $query = (new \yii\db\Query())
            ->select(['status.name as status', 'customers.name as customer', 'orders.customer_id', 'orders.status_id', 'COUNT(*) as shipments'])
            ->from('orders')
            ->leftJoin('customers', 'orders.customer_id = customers.id')
            ->leftJoin('status', 'orders.status_id = status.id')
            ->where(['between', 'orders.created_date', $start_date, $end_date])
            ->andWhere(['in','orders.customer_id', $this->customer_ids])
            ->groupBy(['customer_id', 'status_id'])
            ->orderBy('customer_id')
            ->all();
        $customers = Customer::find()->where(['in', 'id', $this->customer_ids])->all();
        /**
         * @var Customer $customer
         */
        $statuses = Status::find()->all();

        /**
         * @var Status $status
         */
        foreach ($statuses as $status) {
            $status2[$status->id] = [
                'name' => $status->name,
                'orders' => 0,
            ];
        }

        foreach ($customers as $customer) {
            $response[$customer->id] = [
                'name' => $customer->name,
                'customer_id' => $customer->id,
                'statuses' => $status2,
            ];
            foreach ($query as $q) {
                $response[$q['customer_id']]['statuses'][$q['status_id']]['orders'] = $q['shipments'];
            }
        }

        return $this->asJson($response);
    }
}