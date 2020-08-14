<?php

namespace shopify\controllers;

use Yii;
use yii\web\ServerErrorHttpException;


/**
 * Site controller
 */
class SiteController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCallback()
    {
        if ($this->validateCallback()) {
            $this->redirect(['/site/index']);
        } else {
            throw new ServerErrorHttpException('Problem with authentication');
        }
    }
}





