<?php


namespace frontend\controllers;


use common\models\Invoice;
use common\models\query\InventoryQuery;
use common\models\query\InvoiceQuery;
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

    /**
     * Lists all PaymentMethod models.
     * @return mixed
     */
    public function actionIndex()
    {
        // If user is not admin, then show orders that ONLY belong to current user
        $query = Invoice::find();
        if (!\Yii::$app->user->identity->isAdmin) {
            $query->forCustomers(\Yii::$app->user->identity->customerIds);
        }
        $query->orderBy(['id' => SORT_DESC]);

        $invoiceDataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'invoiceDataProvider' => $invoiceDataProvider,
        ]);
    }

    public function actionView($id)
    {

        $query = Invoice::find()
            ->where(['id' => $id]);

        if (!\Yii::$app->user->identity->isAdmin) {
            $query->andWhere(['in', 'customer_id', \Yii::$app->user->identity->customerIds]);
        }

        if (($model = $query->one()
            ) === null) {
            throw new NotFoundHttpException('Invoice not found');
        }

        return $this->render('view', [
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