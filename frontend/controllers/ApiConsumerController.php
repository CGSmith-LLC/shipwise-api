<?php

namespace frontend\controllers;

use frontend\models\Customer;
use Yii;
use common\models\ApiConsumer;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ApiConsumerController implements the CRUD actions for ApiConsumer model.
 */
class ApiConsumerController extends Controller
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
                    'class' => \dektrium\user\filters\AccessRule::class,
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
     * Lists all ApiConsumer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = ApiConsumer::find();

        if (!Yii::$app->user->identity->isAdmin) {
            $query->andOnCondition([ApiConsumer::tableName() . '.customer_id' => Yii::$app->user->identity->customer_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSecret($id)
    {
        $model = $this->findModel($id);

        // Don't allow access to view secret after 60 seconds since creation time
        if (time() > strtotime($model->created_date) + 60) {
            throw new NotFoundHttpException('Page not found');
        }

        return $this->render('secret', ['model' => $model]);
    }


    /**
     * Creates a new ApiConsumer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ApiConsumer();

        if ($model->load(Yii::$app->request->post())) {
            $model->generateAuthKey();
            $model->generateAuthSecret();
            $model->save();
            Yii::$app->getSession()->setFlash('error', 'Please store your API key and secret in a password manager. You will not be able to see the secret after you leave this page');

            return $this->redirect(['secret', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),

        ]);
    }

    /**
     * Deletes an existing ApiConsumer model.
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
     * Finds the ApiConsumer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return array|ApiConsumer|\yii\db\ActiveRecord
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id)
    {
        $query = ApiConsumer::find()->where(['id' => $id]);

        if (!\Yii::$app->user->identity->isAdmin) {
            $query->andWhere(['in', 'customer_id', \Yii::$app->user->identity->customerIds]);
        }

        if (($model = $query->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
