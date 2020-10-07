<?php

namespace frontend\controllers;

use common\models\Invoice;
use common\pdf\InvoicePDF;
use dektrium\user\filters\AccessRule;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Class InvoiceController
 *
 * @package frontend\controllers
 */
class InvoiceController extends Controller
{
    /** {@inheritdoc } */
    public function behaviors()
    {
        return [
            'access' => [
                'class'      => AccessControl::class,
                'ruleConfig' => [
                    'class' => AccessRule::class,
                ],
                'rules'      => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoice models.
     *
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

        $invoiceDataProvider = new ActiveDataProvider(
            [
                'query' => $query,
            ]
        );

        return $this->render(
            'index',
            [
                'invoiceDataProvider' => $invoiceDataProvider,
            ]
        );
    }

    /**
     * Displays a single Invoice model.
     *
     * @param int $id Invoice id
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id)
    {
        $query = Invoice::find()->where(['id' => $id]);

        if (!\Yii::$app->user->identity->isAdmin) {
            $query->andWhere(['in', 'customer_id', \Yii::$app->user->identity->customerIds]);
        }

        if (($model = $query->one()) === null) {
            throw new NotFoundHttpException('Invoice not found');
        }

        return $this->render(
            'view',
            [
                'model' => $model,
            ]
        );
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id Invoice id
     *
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Generates and outputs Invoice PDF file.
     *
     * @param int $id Invoice Id
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionInvoicePdf(int $id)
    {
        $invoice = $this->findModel($id);

        $pdf = new InvoicePDF();
        $pdf->generate($invoice);

        return Yii::$app->response->sendContentAsFile(
            $pdf->Output('S'),
            "Invoice_{$invoice->id}.pdf",
            ['mimeType' => 'application/pdf', 'inline' => true]
        );
    }

}
