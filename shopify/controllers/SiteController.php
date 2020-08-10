<?php

namespace shopify\controllers;

use Yii;


/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
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


    /**
     * Index for the dashboard numbers
     *
     * @inheritDoc
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}





