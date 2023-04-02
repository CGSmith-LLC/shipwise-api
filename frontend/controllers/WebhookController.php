<?php

namespace frontend\controllers;

use common\models\Order;
use console\jobs\webhooks\OrderWebhook;
use frontend\models\Customer;
use Yii;
use common\models\Webhook;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WebhookController implements the CRUD actions for Webhook model.
 */
class WebhookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'regenerate' => ['POST'],
                    'test' => ['POST'],
                ],
            ],
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
     * Lists all Webhook models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Webhook::find()->with(['webhookTrigger']),
        ]);


        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Webhook model.
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
     * Creates a new Webhook model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Webhook();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin ?
                Customer::getList() : Yii::$app->user->identity->getCustomerList(),
        ]);
    }

    /**
     * Updates an existing Webhook model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->createNewRelations();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin ?
                Customer::getList() : Yii::$app->user->identity->getCustomerList(),
        ]);
    }

    /**
     * Deletes an existing Webhook model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionRegenerate($id)
    {
        if ($this->findModel($id)->regenerateSigningSecret()) {
            Yii::$app->getSession()->setFlash('success', 'Signing secret has been updated.');
        } else {
            Yii::$app->getSession()->setFlash('error', 'We had a problem updating the signing secret!');
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     */
    public function actionTest($id)
    {
        try {
            if ($model = $this->findModel($id)) {
                $order = Order::find()->where(['customer_id' => $model->customer_id])->limit(1)->one();
                \Yii::$app->queue->push(
                    new OrderWebhook([
                        'webhook_id' => $model->id,
                        'order_id' => $order->id,
                        'testWebhook' => true,
                    ])
                );
                Yii::$app->getSession()->setFlash('success', 'Test sent to your endpoint - please check the logs');
            }
        } catch (\Exception) {
            Yii::$app->getSession()->setFlash('error', 'We had a problem generating a test. You may not have an order to send as a test.');

        }

        return $this->redirect(['webhook/view', 'id' => $id]);
    }

    /**
     * Finds the Webhook model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Webhook the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Webhook::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
