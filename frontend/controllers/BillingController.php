<?php

namespace frontend\controllers;

use Da\User\Filter\AccessRuleFilter;
use common\models\Invoice;
use Stripe\Stripe;
use Yii;
use frontend\models\PaymentMethod;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\httpclient\RequestEvent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BillingController implements the CRUD actions for PaymentMethod model.
 */
class BillingController extends \frontend\controllers\Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'ruleConfig' => [
                    'class' => AccessRuleFilter::class,
                ],

                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
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
        $paymentMethodDataProvider = new ActiveDataProvider([
            'query' => PaymentMethod::find()->where(['customer_id' => Yii::$app->user->identity->getCustomerId()]),
        ]);

        $invoiceDataProvider = new ActiveDataProvider([
            'query' => Invoice::find()->where(['customer_id' => Yii::$app->user->identity->getCustomerId()])->orderBy(['id' => SORT_DESC]),
        ]);

        return $this->render('index', [
            'paymentMethodDataProvider' => $paymentMethodDataProvider,
            'invoiceDataProvider' => $invoiceDataProvider,
        ]);
    }

    /**
     * Displays a single PaymentMethod model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PaymentMethod model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentMethod();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->save()) {
                    // if PaymentMethod count is 1 - then set this payment method as default true.
                    if (PaymentMethod::find()->where(['customer_id' => $model->customer_id])->count() == 1) {
                        $model->default = PaymentMethod::PRIMARY_PAYMENT_METHOD_YES;
                    }
                    $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($model->stripe_payment_method_id);
                    $model->brand = $stripePaymentMethod->card->brand;
                    $model->lastfour = $stripePaymentMethod->card->last4;
                    $month = $stripePaymentMethod->card->exp_month;
                    $year = $stripePaymentMethod->card->exp_year;
                    $model->expiration = $month . "/" . $year;
                    $model->update();

                    Yii::$app->getSession()->setFlash('success', 'Credit card successfully added.');

                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PaymentMethod model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionSelect($id)
    {
        $model = $this->findModel($id);
        //make the current default card not default\
        $currentDefaultModels = PaymentMethod::find()
            ->where(['default' => 1])
            ->all();
        /** @var PaymentMethod $paymentMethod */
        foreach ($currentDefaultModels as $currentDefaultModel) {
            $currentDefaultModel->default = PaymentMethod::PRIMARY_PAYMENT_METHOD_NO;
            $currentDefaultModel->update();
        }

        // if action triggers update the current ID to default payment
        $model->setAttributes([
            'default' => 1,
        ]);
        $model->update();

        return $this->redirect(['index']);
    }


    /**
     * Deletes an existing PaymentMethod model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // Default cards cannot be deleted
        $paymentMethod = $this->findModel($id);
        if ($paymentMethod->default == PaymentMethod::PRIMARY_PAYMENT_METHOD_YES) {
            Yii::$app->session->addFlash('errors', 'You cannot delete a primary payment method.');
        } else {
            $paymentMethod->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentMethod model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentMethod the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentMethod::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
