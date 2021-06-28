<?php

namespace frontend\controllers;

use console\jobs\CreateReportJob;
use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use frontend\models\User;
use Yii;

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
            // Always set for beginning of day and end of day for query
            $model->start_date = Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d 00:00:00');
            $model->end_date   = Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d 23:59:59');

            Yii::$app->queue->push(new CreateReportJob([
                'customer' => $model->customer,
                'user_id' => Yii::$app->user->id,
                'user_email' => User::findone(['id' => Yii::$app->user->id])->email,
                'start_date' => $model->start_date,
                'end_date' => $model->end_date,
            ]));
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
}
