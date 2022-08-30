<?php

namespace frontend\controllers;

use frontend\models\ColumnManage;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CountryController implements the CRUD actions for Country model.
 */
class ColumnManageController extends Controller
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

    /**
     * Updates an existing Country model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        if ($_POST) {
            $customColumns = $_POST;
            $columnData = array();
            foreach($customColumns as $key => $value) {
                $columnData[] = array(
                    'attribute' => $key,
                    'status' => $value,
                );
            }
            $columnData = json_encode($columnData);
            $customColumns = ColumnManage::getColumnManageOfUser();
            $customColumns->column_data = $columnData;
            $customColumns->save();
        }
    }
}
