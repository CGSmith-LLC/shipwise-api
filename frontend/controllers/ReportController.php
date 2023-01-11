<?php

namespace frontend\controllers;

use common\models\SpeedeeManifest;
use console\jobs\CreateReportJob;
use console\jobs\speedee\SpeeDeeShipJob;
use frontend\models\Customer;
use frontend\models\forms\ReportForm;
use frontend\models\forms\SpeedeeManifestForm;
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

    public function actionManifestSpeedee()
    {
        $model = new SpeedeeManifestForm();

        if (Yii::$app->request->post()) {
            $request = Yii::$app->request->post();
            Yii::$app->queue->push(new SpeeDeeShipJob(['customer_id' => $request['SpeedeeManifestForm']['customer']]));
            Yii::$app->session->setFlash('success', "Manifest queued for delivery.");
        }

        return $this->render('speedee-manifest', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
        ]);
    }

    public function actionSpeedeeFetch($customerId)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return SpeedeeManifest::find()->where(['customer_id' => $customerId])->andWhere(['is_manifest_sent' => false])->all();
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
                'items' => $model->items,
            ]));

            Yii::$app->getSession()->setFlash('success', 'The report is being generated. Please check your email in a few minutes.');
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
