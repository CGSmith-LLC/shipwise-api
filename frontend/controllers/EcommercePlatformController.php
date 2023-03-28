<?php

namespace frontend\controllers;

use Yii;
use yii\web\Response;
use Da\User\Filter\AccessRuleFilter;
use common\models\EcommercePlatform;
use common\models\search\EcommercePlatformSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class EcommercePlatformController
 * @package frontend\controllers
 */
class EcommercePlatformController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
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
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all EcommercePlatform models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new EcommercePlatformSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EcommercePlatform model.
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing EcommercePlatform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate(int $id): string|Response
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Platform has been updated.');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Switches the status of a specific EcommercePlatform model.
     * @throws NotFoundHttpException
     */
    public function actionStatus(int $id): Response
    {
        $model = $this->findModel($id);
        $model->switchStatus();
        Yii::$app->session->setFlash('success', 'Status has been updated.');

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the EcommercePlatform model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EcommercePlatform the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): EcommercePlatform
    {
        if (($model = EcommercePlatform::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
