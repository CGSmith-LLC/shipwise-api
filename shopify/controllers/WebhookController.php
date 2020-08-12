<?php

namespace shopify\controllers;

use common\models\CustomerMeta;
use common\models\shopify\Webhook;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\ResponseAccess;
use Osiset\BasicShopifyAPI\Session;
use Yii;


/**
 * Webhook Controller
 */
class WebhookController extends BaseController
{
    public $code;
    public $accessToken;
    public $url;
    /** @var $shopify BasicShopifyAPI */
    public $shopify;
    public $topics = ['orders/delete', 'orders/updated', 'orders/create', 'orders/edited', 'orders/cancelled'];
    const WEBHOOK_ADDRESS = 'https://1320aea60d5b.ngrok.io/v1/webhook';


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

            $model = new Webhook();
            $model->setAttributes([
                'shopify_webhook_id' => (string)$webhookk['id'],
                'customer_id' => $this->customerMeta->customer_id,
            ]);
            Yii::debug($model);
            $model->save();
        }

        return $this->render('webhook');
    }

    public function actionDelete()
    {
        $customerWebhooks = Webhook::find()
            ->where(['customer_id' => $this->customerMeta->customer_id])
            ->all();


        foreach ($customerWebhooks as $webhook) {
            $webhookId = $webhook->shopify_webhook_id;
            $response = $this->shopify->rest('DELETE', 'admin/api/webhooks/' . $webhookId . '.json');
            Yii::debug($response);
        }

        return $this->render('webhook');
    }
}





