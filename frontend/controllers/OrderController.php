<?php

namespace frontend\controllers;

use common\pdf\OrderPackingSlip;
use common\models\forms\OrderForm;
use common\models\{base\BaseBatch, Country, ScheduledOrder, State, Status, shipping\Carrier, shipping\Service};
use frontend\models\Customer;
use Yii;
use frontend\models\{Address,
    forms\BulkEditForm,
    Item,
    Order,
    BulkAction,
    OrderImport,
    search\OrderSearch,
    search\ScheduledOrderSearch};
use yii\web\{BadRequestHttpException,
    Cookie,
    NotFoundHttpException,
    Response,
    ServerErrorHttpException};
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii2tech\csvgrid\CsvGrid;

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
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
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

    public function actionStatusUpdate($id, $status)
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if ($order = Order::find()->forCustomers(Yii::$app->user->identity->getCustomerList())->byId($id)->one()) {
            $status = Status::findOne($status);
            $order->status_id = $status->id;
            if ($order->save()) {
                return [
                    'message' => $order->status->getStatusLabel(),
                    'code' => 200,
                ];
            }
        }

        return [
            'code' => 400
        ];
    }

    public function actionBulkEdit()
    {
        $model = new BulkEditForm();
        $model->setAttributes(Yii::$app->request->post('BulkEditForm'));
        Yii::debug($model);

        // Validate model and save
        if (Yii::$app->request->post() && $model->validate()) {
            Yii::debug($model);

            $orders = Order::find()->forCustomer($model->customer)->andWhere(['in', 'customer_reference', $model->orders])->all();
            $status = Status::find()->where(['id' => $model->action])->one();

            if ($model->confirmed) {
                /** @var Order $order */
                $errors = $success = [];
                foreach ($orders as $order) {
                    if ($model->reopen_enable && !empty($model->open_date)) {
                        $scheduledOrder = new ScheduledOrder([
                            'customer_id' => $order->customer_id,
                            'order_id' => $order->id,
                            'status_id' => Status::OPEN,
                            'scheduled_date' => $model->open_date,
                         ]);
                        $scheduledOrder->save();

                        $success[] = $order->customer_reference;
                    }else {
                        if (!$order->changeStatus($status->id)) {
                            $errors[] = $order->customer_reference;
                        } else {
                            $success[] = $order->customer_reference;
                        }
                    }
                }

                if ($model->reopen_enable && !empty($model->open_date)) {
                    $scheduledVerbiage = 'scheduled for';
                } else {
                    $scheduledVerbiage = 'changed to';
                }
                if (count($errors) > 0) {
                    Yii::$app->getSession()->setFlash('error', count($errors) . ' orders failed to ' . $scheduledVerbiage . ': <br>' . implode(',', $errors));
                }

                if (count($success) > 0) {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        count($success) . ' orders ' . $scheduledVerbiage . ' <strong>' . $status->name . '</strong>'
                    );
                }

                return $this->redirect('bulk-edit');
            }

            // Set model->order_ids to newlines for order confirmation screen
            $model->order_ids = implode(PHP_EOL, ArrayHelper::map($orders, 'id', 'customer_reference'));
            $model->confirmed = true;

            $warning = 'You are about to change <em>all</em> of the orders below to a status of <strong>' . $status->name . '</strong>';
            if ($model->reopen_enable && !empty($model->open_date)) {
                $warning .= '<br><br>The orders will change to <strong>Open</strong> on <strong>' . $model->open_date . '</strong>.';
            } else {
                $model->reopen_enable = false;
            }
            Yii::$app->getSession()->setFlash('warning', $warning);

            return $this->render(
                'bulk-edit',
                [
                    'model' => $model,
                    'confirmed' => true,
                    'customers' => Yii::$app->user->identity->isAdmin ? Customer::getList() : Yii::$app->user->identity->getCustomerList(),
                    'statuses' => Status::getList(),
                ]
            );
        }

        return $this->render(
            'bulk-edit',
            [
                'model' => $model,
                'confirmed' => false,
                'customers' => Yii::$app->user->identity->isAdmin ? Customer::getList() : Yii::$app->user->identity->getCustomerList(),
                'statuses' => Status::getList(),
            ]
        );
    }

    public function actionScheduled()
    {
        $searchModel = new ScheduledOrderSearch();
        $dataProvider = $searchModel->search([]);

        return $this->render(
            'scheduled',
            [
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Lists all Order models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'statuses' => Status::getList(),
                'carriers' => Carrier::getList(),
                'services' => Service::getList(),
            ]
        );
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
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
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
        $model = new OrderForm();
        $model->order = new Order();

        // Set default values
        $model->order->loadDefaultValues();
        $model->address->loadDefaultValues();

        $model->order->status_id = Status::OPEN;
        $model->order->origin = Yii::$app->name;
        $model->order->address_id = 0; // to avoid validation, as we validate address model separately

        // Load from POST
        $model->setAttributes(Yii::$app->request->post());

        // Validate model and save
        if (Yii::$app->request->post() && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Order created.');

            return $this->redirect(['view', 'id' => $model->order->id]);
        }

        return $this->render(
            'create',
            [
                'model' => $model,
                'customers' => Yii::$app->user->identity->isAdmin
                    ? Customer::getList()
                    : Yii::$app->user->identity->getCustomerList(),
                'statuses' => Status::getList(),
                'carriers' => Carrier::getList(),
                'services' => Service::getList('id', 'name', $model->order->carrier_id),
                'countries' => Country::getList(),
                'states' => State::getList('id', 'name', $model->address->country),
            ]
        );
    }

    public function actionSimpleView()
    {
        $cookies = Yii::$app->request->cookies;
        $simple = $cookies->getValue('simple', 0);

        // Response
        $cookies = Yii::$app->response->cookies;

        if (!$simple) {
            $cookies->add(new Cookie([
                'name' => 'simple',
                'value' => 1
            ]));
        }else {
            $cookies->remove('simple');
        }

        return $this->redirect(Yii::$app->request->referrer);
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
        $model = new OrderForm();

        $model->order = $this->findModel($id);
        $model->setAttributes(Yii::$app->request->post());

        if (Yii::$app->request->post() && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', 'Order has been updated.');

            return $this->redirect(['view', 'id' => $model->order->id]);
        }

        return $this->render(
            'update',
            [
                'model' => $model,
                'customers' => Yii::$app->user->identity->isAdmin
                    ? Customer::getList()
                    : Yii::$app->user->identity->getCustomerList(),
                'statuses' => Status::getList(),
                'carriers' => Carrier::getList(),
                'services' => Service::getList('id', 'name', $model->order->carrier_id),
                'countries' => Country::getList(),
                'states' => State::getList('id', 'name', $model->address->country),
            ]
        );
    }

    /**
     * Clone an existing Order model.
     * If clone is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function actionClone($id)
    {
        /** @var OrderForm */
        $orderToClone = Order::find()->where(['id' => $id])->one();
        $addressToClone = Address::find()->where(['id' => $orderToClone->address_id])->one();
        $itemsToClone = Item::find()->where(['order_id' => $id])->all();
        $userStatusPreference = Yii::$app->user->identity->profile->clone_order_preference;
        $model = new OrderForm();
        $model->order = new Order();
        $model->setOrder($orderToClone->attributes);
        $model->setAddress($addressToClone->attributes);

        $model->order->status_id = $userStatusPreference;
        $model->order->setAttribute('customer_reference', $model->order->getNextCustomerReferenceNumber());

        /** @var Item $item */
        foreach ($itemsToClone as $item) {
            $items[] = [
                    'quantity' => $item->quantity,
                    'sku' => $item->sku,
                    'order_id' => $item->order_id,
                    'name' => $item->name,
            ];
        }
        $model->setItems($items);
        $model->save();

        Yii::$app->getSession()->setFlash('success', 'Order cloned.');

        return $this->redirect(['view', 'id' => $model->order->id]);
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
        $mailer = \Yii::$app->mailer;
        $mailer->viewPath = '@frontend/views/mail';
        $mailer->getView()->theme = \Yii::$app->view->theme;
        $mailer->compose(['html' => 'notification'], [
            'title' => 'delete attempt',
            'url' => 'delete',
            'urlText' => 'delete',
            'message' => Yii::$app->user->identity->email,
        ])
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setTo(Yii::$app->params['senderEmail'])
            ->setSubject('delete attempt')
            ->send();
        throw new ServerErrorHttpException('Problem with the action - please contact support.');
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
     * Get list of states for given country.
     *
     * @param string $country country.
     *
     * @return array JSON array
     * @throws BadRequestHttpException
     */
    public function actionCountryStates($country)
    {
        $request = Yii::$app->request;
        if (!$request->isAjax || !($country)) {
            throw new BadRequestHttpException('Bad request.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return State::getList('id', 'name', $country);
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
            'errors' => Html::errorSummary($model, ['header' => false]),
            'link' => $model->getLink(),
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

        return $this->render(
            "bulk-result/$viewName",
            [
                'model' => $model,
            ]
        );
    }

    public function actionBatch($id = null)
    {
        if ($id === null) {
            $batches = BaseBatch::find()
                ->where(['customer_id' => Yii::$app->user->identity->customer_id])
                ->orderBy(['created_date' => SORT_DESC]);

            $query = BaseBatch::find();

            if (!Yii::$app->user->identity->isAdmin) {
                $query->andOnCondition([BaseBatch::tableName() . '.customer_id' => $this->customers]);
            }

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            return $this->render(
                'batch',
                [
                    'dataProvider' => $dataProvider
                ]
            );
        }

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(['batch_id' => $id]);

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'statuses' => Status::getList(),
                'carriers' => Carrier::getList(),
                'services' => Service::getList(),
            ]
        );
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
            $fp = fopen($filename, 'wb');
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
            exec(
                "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -dFitPage -sOutputFile=$mergedFilename " . implode(
                    " ",
                    $tmpFiles
                )
            );
            $mergedFileData = base64_encode(file_get_contents($mergedFilename));
            @unlink($mergedFilename);
            foreach ($tmpFiles as $filename) {
                @unlink($filename);
            }
        }

        return Yii::$app->response->sendContentAsFile(
            base64_decode($mergedFileData),
            $mergedFilename,
            ['mimeType' => 'application/pdf', 'inline' => true]
        );
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

        return Yii::$app->response->sendContentAsFile(
            $pdf->Output('S'),
            "PackingSlip_{$order->customer_reference}.pdf",
            ['mimeType' => 'application/pdf', 'inline' => true]
        );
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
            $service = Service::findByShipWiseCode('UPSGround');
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

        $order->tracking = $shipment->getMasterTracking();
        if ($order->service->carrier->getReprintBehaviour() == Carrier::REPRINT_BEHAVIOUR_EXISTING) {
            $order->label_data = $shipment->mergedLabelsData;
            $order->label_type = $shipment->mergedLabelsFormat;
        }
        $order->status_id = Status::SHIPPED;
        $order->save(false);

        $filename = "$order->tracking." . strtolower($shipment->mergedLabelsFormat);

        return Yii::$app->response->sendContentAsFile(
            base64_decode($shipment->mergedLabelsData),
            $filename,
            [
                'mimeType' => FileHelper::getMimeTypeByExtension($filename),
                'inline' => true,
            ]
        );
    }

    /**
     * Renders the Import Orders page.
     * Processes the form submission with uploaded file.
     *
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionImport()
    {
        $customers = Yii::$app->user->identity->isAdmin
            ? Customer::getList()
            : Yii::$app->user->identity->getCustomerList();

        $model = new \common\models\OrderImport();

        if (count($customers) === 1) {
            $model->customer = array_key_first($customers);
        }else {
            $model->load(Yii::$app->request->queryParams);
        }

        // check that selected customer belongs to user
        if ($model->customer && !isset($customers[$model->customer])) {
            throw new BadRequestHttpException('Invalid customer id.');
        }

        if (Yii::$app->request->get('success')) {
            Yii::$app->session->setFlash(
                'success',
                "Import successfully processed! Orders will processed shortly! <br />" .
                Html::a('See orders', ['/order'], ['target' => '_blank'])
            );
        }

        return $this->render(
            'import',
            [
                'model' => $model,
                'carriers' => Carrier::getShipwiseCodes(),
                'services' => Service::getShipwiseCodes(),
                'customers' => $customers,
                'showSelectCustomer' => count($customers) > 1
            ]
        );
    }

    /**
     * Downloads a CSV file template for orders import.
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDownloadCsvTemplate()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Bad request.');
        }

        $exporter = new CsvGrid(
            [
                'dataProvider' => new ArrayDataProvider(['allModels' => OrderImport::getSampleData()]),
                'columns' => OrderImport::$csvFields,
            ]
        );

        return $exporter->export()->send('template-import-orders.csv');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionCarrierModal()
    {
        if(!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException('Page not found.');
        }

        return $this->renderAjax('carrier-modal');
    }
}
