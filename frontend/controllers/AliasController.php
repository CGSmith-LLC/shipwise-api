<?php

namespace frontend\controllers;

use Yii;
use common\models\AliasChildren;
use common\models\AliasParent;
use frontend\models\Customer;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AliasController implements the CRUD actions for AliasParent model.
 */
class AliasController extends Controller
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
     * Lists all AliasParent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
                                                   'query' => AliasParent::find(),
                                               ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AliasParent model.
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
     * Creates a new AliasParent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AliasParent();

        if ($model->load(Yii::$app->request->post())) {
            try {
                $transaction = Yii::$app->db->beginTransaction();
                $model->save();

                $aliasChildrenQty = Yii::$app->request->post('AliasChildrenQty');
                $aliasChildrenSku = Yii::$app->request->post('AliasChildrenSku');
                $aliasChildrenName = Yii::$app->request->post('AliasChildrenName');
                for ($i = 0; $i < count($aliasChildrenSku); $i++) {
                    $aliasChild = new AliasChildren();
                    $aliasChild->attributes = [
                        'alias_id' => $model->id, // Alias Parent ID
                        'quantity' => $aliasChildrenQty[$i],
                        'name' => $aliasChildrenName[$i],
                        'sku' => $aliasChildrenSku[$i],
                    ];
                    if ($aliasChild->validate()) {
                        $aliasChild->save();
                    } else {
                        throw new HttpException('Child alias not valid');
                    }
                }
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Exception $e) {
                Yii::debug($e);
                $transaction->rollBack();
                return $this->redirect(['create']);
            }
        }

        return $this->render('create', [
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AliasParent model.
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
            'customers' => Yii::$app->user->identity->isAdmin
                ? Customer::getList()
                : Yii::$app->user->identity->getCustomerList(),
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AliasParent model.
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
     * Finds the AliasParent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AliasParent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AliasParent::findOne($id)) !== null) {
            if (!\Yii::$app->user->identity->isAdmin) {
                if (!in_array($model->customer_id, Yii::$app->user->identity->customerIds)) {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
