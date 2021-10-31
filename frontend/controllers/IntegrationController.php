<?php

namespace frontend\controllers;

use common\models\IntegrationMeta;
use frontend\models\forms\IntegrationForm;
use frontend\models\forms\integrations\WooCommerceForm;
use Yii;
use common\models\Integration;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * IntegrationController implements the CRUD actions for Integration model.
 */
class IntegrationController extends Controller
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
                        'roles' => ['admin'],
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

    public function actionStatus($id, $status)
    {
        $model = $this->findModel($id);
        $model->status = $status;
        if ($model->update()) {
            Yii::$app->getSession()->addFlash('success', 'Status changed to '. $model->getStatusLabel(false) .' on ' . $model->name . ' integration');
        }else {
            Yii::$app->getSession()->addFlash('error', 'Status modification failure');

        }

        return $this->redirect(['index']);
    }

    /**
     * Lists all Integration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Integration::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Integration model.
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

    public function actionMeta($id)
    {
        if ($model = $this->findModel($id)) {
            $formName = 'frontend\models\forms\integrations\\' . $model->ecommerce . 'Form';
            /** @var WooCommerceForm $metaModel */
            $metaModel = new $formName;

            if ($metaModel->load(Yii::$app->request->post())) {
                // delete old meta data first
                // @todo perform an update on the fields
                $existingMetas = IntegrationMeta::find()->where(['integration_id' => $model->id])->all();
                foreach ($existingMetas as $existingMeta) {
                    $existingMeta->delete();
                }

                foreach ($metaModel->getAttributes() as $name => $value) {
                    IntegrationMeta::addMeta($name, $value, $model->id);
                }

                Yii::$app->getSession()->setFlash('success', 'Integration details saved successfully');

                return $this->redirect(['meta', 'id' => $model->id]);
            } else {
                // assume we have attributes to set
                $metaModel->setAttributes(IntegrationMeta::getMeta($model->id));
            }

            return $this->render('meta', [
                'model' => $model,
                'metaModel' => $metaModel,
            ]);
        }
    }

    /**
     * Creates a new Integration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Integration();
        $model->status = Integration::PENDING; // when creating a new integration it starts as pending until we have meta data

        if ($model->load(Yii::$app->request->post())) {
            $model->save();

            return $this->redirect(['meta', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'ecommercePlatforms' => $this->getPlatforms(),
            'customers' => Yii::$app->user->identity->isAdmin
                ? \frontend\models\Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),

        ]);
    }

    /**
     * Return a list of eCommerce platforms so the form can render the proper keys and values required
     *
     * @return array key value of [class] => Public Name
     */
    protected function getPlatforms(): array
    {
        $return = [];

        $namespace = 'frontend\models\forms\integrations';
        $path = __DIR__ . '/../models/forms/integrations';
        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $phpFiles = new \RegexIterator($allFiles, '/\.php$/');
        /** @var \SplFileInfo $file */
        foreach ($phpFiles as $file) {
            $reflection = new \ReflectionClass($namespace . '\\' . substr($file->getFilename(), 0, (strlen($file->getFilename()) - 4)));
            $name = $reflection->getProperty('dropDownName')->getValue();
            $return[substr($file->getFilename(), 0, (strlen($file->getFilename()) - 8))] = $name;
        }

        return $return;
    }

    /**
     * Updates an existing Integration model.
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
            'ecommercePlatforms' => $this->getPlatforms(),
            'customers' => Yii::$app->user->identity->isAdmin
                ? \frontend\models\Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),

        ]);
    }

    /**
     * Deletes an existing Integration model.
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

    public function actionNextForm($form)
    {
        $request = Yii::$app->request;
        $class = 'frontend\models\forms\integrations\\' . $form . 'Form';
        $platform = new $class;
        if (!$request->isAjax || !($platform)) {
            throw new BadRequestHttpException('Bad request.');
        }

        Yii::$app->response->format = Response::FORMAT_JSON;


        return $platform->meta;
    }

    /**
     * Finds the Integration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Integration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Integration::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
