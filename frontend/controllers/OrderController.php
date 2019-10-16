<?php

namespace frontend\controllers;

use Yii;
use common\models\{Carrier, Service, State, Status};
use frontend\models\Order;
use frontend\models\forms\OrderForm;
use frontend\models\search\OrderSearch;
use yii\helpers\Json;
use yii\web\{BadRequestHttpException, Controller, NotFoundHttpException};

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs'  => [
                'class'   => 'yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['POST'],
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
     * Lists all Order models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'statuses'     => Status::getList(),
        ]);
    }

    /**
     * Displays a single Order model.
     *
     * @param integer $id
     *
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
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCreate()
    {
        /** @var OrderForm */
        $id           = Yii::$app->request->post('order_id') ?? null;
        $model        = new OrderForm();
        $model->order = ($id ? $this->findModel($id) : new Order());

        if ($model->order->isNewRecord) {
            $model->order->loadDefaultValues();
            $model->order->status_id = Status::OPEN;
            $model->order->origin    = Yii::$app->name;
        }

        // Load from POST
        $model->setAttributes(Yii::$app->request->post());

        // Validate model, ship and save
        if (Yii::$app->request->post() && $model->validate() && $model->save()) {
            return $this->redirect(['view', 'id' => $model->order->id]);
        }

        return $this->render('create', [
            'model'    => $model,
            'statuses' => Status::getList(),
            'carriers' => Carrier::getList(),
            'services' => Service::getList('id', 'name', $model->order->carrier_id),
            'states'   => State::getList(),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
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

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Get list of carrier services
     *
     * @param int|null $carrierId Carrier ID.
     *
     * @throws BadRequestHttpException
     */
    public function actionCarrierServices($carrierId)
    {
        $request = Yii::$app->request;
        if (!$request->isAjax || !($carrierId)) {
            throw new BadRequestHttpException('Bad request.');
        }

        echo Json::encode(Service::getList('id', 'name', $carrierId));
    }
}
