<?php

namespace shopify\controllers;


use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use Yii;
use yii\web\Controller;
use yii\web\ServerErrorHttpException;

/**
 * Site controller
 */
class BaseController extends Controller
{

    public function init()
    {

        // hmac=b39f98818f3f6cf64576900506f8cb2ed66f2ceb89e90cf592af8a97247b3bca
        // shop=cgsmith105.myshopify.com
        // timestamp=1597082650
        if (!Yii::$app->session->get('shopify-code') || Yii::$app->request->getQueryParam('hmac')) {

            /**
             * TODO: Validate hmac with timestamp
             */
            $redirect = \yii\helpers\Url::toRoute(['site/callback'], 'https');
            $scopes = ['write_orders'];

            $shopUrl = Yii::$app->request->getQueryParam('shop', 'error');

            if ($shopUrl === 'error') {
                throw new ServerErrorHttpException('Missing shop parameter');
            }

            /**
             * 1. configure options
            2. call api with store
             */
            $options = new Options();
            $options->setVersion('2020-04'); // TODO chang ethis in the config
            $options->setApiKey(Yii::$app->params['shopifyPublicKey']);
            $options->setApiSecret(Yii::$app->params['shopifyPrivateKey']);
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($shopUrl));

            $redirectUrl = $api->getAuthUrl($scopes, $redirect);

            Yii::$app->response->redirect($redirectUrl);
            Yii::$app->end();
        }
    }

    public function actionCallback()
    {
        // code=d7b8d8cd7974e15f03bfc64c2931f3a3
        // hmac=788397aa915e41996a698e97cad29db7209e566dc04b963fd3d586c69b64e00d
        // shop=cgsmith105.myshopify.com
        // timestamp=1597083500

        $code = Yii::$app->request->getQueryParam('code', 'error');

        if ($code === 'error') {
            throw new ServerErrorHttpException('Code not valid');
        }


        Yii::$app->session->set('shopify-code', $code);
        Yii::$app->session->set('shopify-url', Yii::$app->request->getQueryParam('shop'));
        $this->redirect(['site/index']);
    }
}





