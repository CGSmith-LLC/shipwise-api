<?php

namespace frontend\controllers;

use common\pdf\OrderPackingSlip;
use frontend\models\Customer;
use Yii;
use common\models\{Country, State, Status, shipping\Carrier, shipping\Service};
use frontend\models\{Order, forms\OrderForm, BulkAction, search\OrderSearch};
use yii\web\{BadRequestHttpException, Controller, NotFoundHttpException, Response};
use yii\helpers\FileHelper;
use yii\helpers\Html;
/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends \frontend\controllers\Controller

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
        $model->address->loadDefaultValues();

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
            'countries' => Country::getList(),
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
        Yii::debug($model->order);


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
            'countries' => Country::getList(),
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
     * @return array JSON array
     * @throws BadRequestHttpException
     */
    public function actionCarrierServices($carrierId)
    {
        $request = Yii::$app->request;
        if (!$request->isAjax || !($carrierId)) {
            throw new BadRequestHttpException('Bad request.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return Service::getList('id', 'name', $carrierId);
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

        return [
            'success' => $model->isSuccess(),
            'message' => $model->getMessage(),
            'errors'  => Html::errorSummary($model, ['header' => false]),
            'link'    => $model->getLink(),
        ];
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

        $viewName = ($model->print_mode == BulkAction::PRINT_MODE_PDF) ? 'view-pdf' : 'view-qz';

        return $this->render("bulk-result/$viewName", [
            'model' => $model,
        ]);
    }

    /**
     * Given BulkAction id, reprints a single combined PDF file merged from bulk items base64 data
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException if the BulkAction model cannot be found
     * @throws \yii\web\RangeNotSatisfiableHttpException
     * @throws \yii\base\Exception
     */
    public function actionBulkReprint($id)
    {
        if (($model = BulkAction::findOne($id)) === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        /**
         * Retrieve base64 PDF data from bulk items, create temp files, merge into one PDF, then clear temp files.
         */
        $dir = Yii::getAlias('@frontend') . '/runtime/pdf/';
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0777, true);
        }
        $tmpFiles = [];
        foreach ($model->getItems()->orderBy('order_id')->all() as $item) {
            $filename = $dir . 'tmp_' . $item->id . '.' . strtolower($item->base64_filetype);
            $fp       = fopen($filename, 'wb');
            fwrite($fp, base64_decode($item->base64_filedata));
            fclose($fp);
            $tmpFiles[] = $filename;
        }

        /**
         * Merge files into one, then delete all temp files.
         */
        $mergedFileData = '';
        $mergedFilename = $dir . ucfirst($model->code) . ".pdf";
        if (!empty($tmpFiles)) {
            // using GhostScript here for merging
            exec("gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=$mergedFilename " . implode(" ", $tmpFiles));
            $mergedFileData = base64_encode(file_get_contents($mergedFilename));
            @unlink($mergedFilename);
            foreach ($tmpFiles as $filename) {
                @unlink($filename);
            }
        }

        return Yii::$app->response->sendContentAsFile(base64_decode($mergedFileData), $mergedFilename,
            ['mimeType' => 'application/pdf', 'inline' => true]);
    }

    /**
     * Generates and outputs Packing Slip PDF file for given Order.
     *
     * @param integer $id Order ID
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionPackingSlip($id)
    {
        $order = $this->findModel($id);

        $pdf = new OrderPackingSlip();
        $pdf->generate($order);

        return Yii::$app->response->sendContentAsFile($pdf->Output('S'),
            "PackingSlip_{$order->customer_reference}.pdf",
            ['mimeType' => 'application/pdf', 'inline' => true]);
    }

    /**
     * Creates and outputs Shipping Label PDF file for given Order.
     *
     * @param integer $id Order ID
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\web\NotFoundHttpException if the model cannot be found
     */
    public function actionShippingLabel($id)
    {
        $order = $this->findModel($id);

        if (empty($order->service)) {
            // @todo Implement here your biz logic for carrier service selection
            $service           = Service::findByShipWiseCode('UPSGround');
            $order->service_id = $service->id;
            $order->carrier_id = $service->carrier_id;
        }

        try {
            $shipment = $order->createShipment();
        } catch (\Exception $e) {
            Yii::error($e);
            throw new \Exception($e);
        }

        if ($order->hasErrors()) {
            \yii\helpers\VarDumper::dump($order->getErrors(), 10, true);
            return false;
        }

        $order->tracking  = $shipment->getMasterTracking();
        $order->status_id = Status::SHIPPED;
        $order->save(false);

        return Yii::$app->response->sendContentAsFile(base64_decode($shipment->mergedLabelsData),
            "$order->tracking." . $shipment->mergedLabelsFormat,
            ['mimeType' => 'application/pdf', 'inline' => true]);
    }
}
