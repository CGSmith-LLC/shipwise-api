<?php

namespace shopify\controllers;

use common\models\shopify\Webhook;
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
        return $this->render('index', [
                'webhooks' => Webhook::find()->where(['customer_id' => $this->shopifyApp->customer_id])->all(),
            ]
        );
    }

    public function actionCallback()
    {
        if ($this->validateCallback()) {
            $this->redirect(['/site/index']);
        } else {
            throw new ServerErrorHttpException('Problem with authentication');
        }
    }

    public function actionFaqs()
    {
        return $this->render('faqs');
    }
}





