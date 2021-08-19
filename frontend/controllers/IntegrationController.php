<?php

namespace frontend\controllers;

use frontend\models\forms\IntegrationForm;
use Yii;
use common\models\Integration;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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

    /**
     * Creates a new Integration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new IntegrationForm();
        $ecommercePlatforms = $this->getPlatforms();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::debug($model);
            }
            return $this->redirect(['view', 'id' => $model->integration->id]);
        }

        return $this->render('create', [
            'model' => $model,
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
    protected function getPlatforms(): array {
        $return = [];

        $namespace = 'frontend\models\forms\integrations';
        $path = __DIR__ . '/../models/forms/integrations';
        $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        $phpFiles = new \RegexIterator($allFiles, '/\.php$/');
        /** @var \SplFileInfo $file */
        foreach ($phpFiles as $file) {
            $reflection = new \ReflectionClass($namespace . '\\'. substr($file->getFilename(), 0,(strlen($file->getFilename()) - 4)));
            $return[$file->getBasename()] = \ReflectionProperty::class->getFilename();
            Yii::debug($reflection);
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

    public function actionWoo($id){
        return $this->render('_wooForm', [
            'model' => $this->findModel($id)
        ]);
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
