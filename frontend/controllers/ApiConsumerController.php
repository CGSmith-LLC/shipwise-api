<?php

namespace frontend\controllers;

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
            $model->customer_id = (new \common\models\ApiConsumer)->getCustomerId();
            $model->save();
            Yii::$app->getSession()->setFlash('warning', 'DO NOT REFRESH THIS PAGE. YOU WILL ONLY SEE THE SECRET KEY ONE TIME. STORE IT IN A SAFE LOCATION AS YOU WILL NOT BE ABLE TO SEE IT AGAIN.');

            return $this->render('secret', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
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
     * @return ApiConsumer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ApiConsumer::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
