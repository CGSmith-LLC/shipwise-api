<?php

namespace frontend\controllers;

use common\models\Status;
use frontend\models\Customer;
use frontend\models\forms\DashboardForm;
use frontend\models\forms\ReportForm;
use frontend\models\Order;
use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
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
            'openCount'      => $open,
            'pendingCount'   => $pending,
            'shippedCount'   => $shipped,
            'completedCount' => $completed,
            'errorCount'     => $error,
        ]);
    }
}


