<?php

namespace frontend\controllers;

use common\pdf\OrderPackingSlip;
use frontend\models\Customer;
use Yii;
use common\models\{State, Status, shipping\Carrier, shipping\Service};
use frontend\models\{Order, forms\OrderForm, BulkAction, search\OrderSearch};
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\{BadRequestHttpException, Controller, NotFoundHttpException, Response};

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
     */
    public function actionCreate()
    {
        /** @var OrderForm */
        $model        = new OrderForm();
        $model->order = new Order();

        // Set default values
        $model->order->loadDefaultValues();
        $model->order->status_id  = Status::OPEN;
        $model->order->origin     = Yii::$app->name;
        $model->order->address_id = 0; // to avoid validation, as we validate address model separately

        // Load from POST
        $model->setAttributes(Yii::$app->request->post());

        // Validate model and save
        if (Yii::$app->request->post() && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Order created.');

            return $this->redirect(['view', 'id' => $model->order->id]);
        }

        return $this->render('create', [
            'model'     => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
            'statuses'  => Status::getList(),
            'carriers'  => Carrier::getList(),
            'services'  => Service::getList('id', 'name', $model->order->carrier_id),
            'states'    => State::getList(),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        /** @var OrderForm */
        $model        = new OrderForm();
        $model->order = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Order has been updated.');

            return $this->redirect(['view', 'id' => $model->order->id]);
        }

        return $this->render('update', [
            'model'     => $model,
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
            'statuses'  => Status::getList(),
            'carriers'  => Carrier::getList(),
            'services'  => Service::getList('id', 'name', $model->order->carrier_id),
            'states'    => State::getList(),
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
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->delete()) {
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'Order deleted.');
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', 'Could not delete order.');
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', 'Could not delete order. ' . $e->getMessage());
        }

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
     * Get list of services for given carrier.
     *
     * @param int $carrierId Carrier ID.
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

    /**
     * Bulk action
     *
     * Executes a bulk action on multiple orders
     *
     * @return array|string
     */
    public function actionBulk()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new BulkAction();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->execute();
        }

        $response = [
            'success' => $model->isSuccess(),
            'message' => $model->getMessage(),
            'errors'  => Html::errorSummary($model, ['header' => false]),
            'link'    => $model->getLink(),
        ];

        return $response;
    }

    /**
     * Displays a single BulkAction model with its relations
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the BulkAction model cannot be found
     */
    public function actionBulkResult($id)
    {
        if (($model = BulkAction::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('bulk-result/view', [
            'model' => $model,
        ]);
    }

    /**
     * Outputs Packing Slip PDF file for given Order.
     *
     * @param integer $id Order ID
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionPackingSlip($id)
    {
        // Generate and output PDF file
        $pdf = new OrderPackingSlip();
        $pdf->generate($this->findModel($id));
        $pdf->Output();
    }
}
