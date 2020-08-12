<?php

namespace shopify\controllers;

use common\models\shopify\Webhook;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\ResponseAccess;
use Osiset\BasicShopifyAPI\Session;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;


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
    public $topics = ['orders/delete', 'orders/updated', 'orders/create', 'orders/edited', 'orders/cancelled'];
    const WEBHOOK_ADDRESS = 'https://1320aea60d5b.ngrok.io/v1/webhook';

    public function init()
    {

    }


    public function actionIndex()
    {
        //$webhooks = ActiveRecord::find()->where(['customerasdfasdf'])->count()->all();

        return $this->render('webhook', [
                'webhook_state' => 0,
            ]
        );

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
        foreach ($this->topics as $topic) {
            $webhook = [
                'webhook' => [
                    'topic' => $topic,
                    'address' => self::WEBHOOK_ADDRESS,
                ]
            ];
            $shopifyWebhook = $this->shopify->rest('POST', 'admin/api/webhooks.json', $webhook);
            /** @var ResponseAccess $body */
            $body = $shopifyWebhook['body'];
            $container = $body['container'];
            $webhookk = $container['webhook'];
            Yii::debug($body->container['webhook']);
            $model = new Webhook();
            $model->setAttributes([
                'shopify_webhook_id' => (string)$webhookk['id'],
                'customer_id' =>
            ]);
            Yii::debug($model);
            $model->save();
        }


        return $this->render('webhook', [

        ]);
    }

    public function actionDelete()
    {

    }
}





