<?php

namespace frontend\controllers;

use Yii;
use console\jobs\CreateReportJob;
use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use frontend\models\User;

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
    public function actionIndex($scenario = null)
    {
        $model = new ReportForm();

        // select model scenario from GET or POST
        $model->scenario = ReportForm::SCENARIO_BY_DATE;
        $scenario = $this->request->post('scenario', $scenario);
        if (in_array($scenario, [ReportForm::SCENARIO_BY_DATE, ReportForm::SCENARIO_BY_ORDER_NR])) {
            $model->scenario = $scenario;
        }

        // Generate Report
        if ($this->request->post()) {
            $model->load(Yii::$app->request->post());

            if ($model->validate()) {

                $model->pushReportQueueJob();

                Yii::$app->getSession()->setFlash('success', 'The report is being generated. Please check your email in a few minutes.');
                return $this->redirect(['report/index', 'scenario' => $model->scenario]);
            }
        }


        return $this->render(
            'index',
            [
                'model'     => $model,
                'customers' => $model->getCustomerList(),
            ]
        );
    }
}
