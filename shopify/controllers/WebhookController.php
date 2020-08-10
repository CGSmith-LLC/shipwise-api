<?php

namespace shopify\controllers;

use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use Yii;


/**
 * Site controllwrite_orderswrite_orderser
 */
class WebhookController extends \yii\web\Controller
{
    public $code;
    public $accessToken;
    public $url;
    /** @var $shopify BasicShopifyAPI */
    public $shopify;


    public function init()
    {

    }


    public function actionIndex()
    {

        return $this->render('webhook');

    }

    public function actionCreate()
    {
        $this->code = Yii::$app->session->get('shopify-code');
        $this->url = Yii::$app->session->get('shopify-url');
        $options = new Options();
        $options->setVersion('2020-04');
        $options->setApiKey(Yii::$app->params['shopifyPublicKey']);
        $options->setApiSecret(Yii::$app->params['shopifyPrivateKey']);
        $this->shopify = new BasicShopifyAPI($options);
        $this->shopify->setSession(new Session($this->url));
        $this->shopify->requestAndSetAccess($this->code);
        $webhook = [
            'webhook' => [
            'topic' => 'orders/create',
            'address' => 'https://1320aea60d5b.ngrok.io/v1/webhook',
            'format' => 'json'
            ]
        ];
        Yii::debug($this->shopify->rest('POST', 'admin/api/webhooks.json', $webhook));

        return $this->render('webhook', [

        ]);
    }

    public function actionDelete()
    {

    }
}





