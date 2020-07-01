<?php


namespace frontend\controllers;


use common\models\Invoice;
use yii\BaseYii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class InvoiceController extends \frontend\controllers\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => \dektrium\user\filters\AccessRule::class,
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $dataProvider = new ActiveDataProvider([
            'query' => Invoice::find()->where(['inv' => $model->id])
        ]);

        return $this->render('view', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


}