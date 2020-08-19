<?php

namespace shopify\controllers;

use common\models\shopify\Webhook;
use Osiset\BasicShopifyAPI\ResponseAccess;
use Yii;


/**
 * Webhook Controller
 */
class WebhookController extends BaseController
{

    public $topics = ['orders/delete', 'orders/updated', 'orders/create', 'orders/edited', 'orders/cancelled'];
    const WEBHOOK_ADDRESS = 'https://1320aea60d5b.ngrok.io/v1/webhook';


    public function actionIndex()
    {
        $webhooks = Webhook::find()->where(['customer_id' => $this->shopifyApp->customer_id])->all();

        return $this->render('webhook', [
                'webhooks' => $webhooks,
                'created' => false,
                'deleted' => false,
            ]
        );

    }

    public function actionCreate()
    {
        $created = false;
        foreach ($this->topics as $topic) {
            $webhook = [
                'webhook' => [
                    'topic' => $topic,
                    'address' => self::WEBHOOK_ADDRESS,
                ]
            ];
            $shopifyWebhook = $this->shopify->rest('POST', 'admin/api/webhooks.json', $webhook);
            Yii::debug($shopifyWebhook);
            /** @var ResponseAccess $body */
            $body = $shopifyWebhook['body'];
            $container = $body['container'];
            $webhookk = $container['webhook'];

            $model = new Webhook();
            $model->setAttributes([
                'shopify_webhook_id' => (string)$webhookk['id'],
                'customer_id' => $this->shopifyApp->customer_id,
            ]);
            Yii::debug($model);
            $created = $model->save();
        }

        return $this->render('webhook', [
            'webhooks' => Webhook::find()->where(['customer_id' => $this->shopifyApp->customer_id])->all(),
            'created' => $created,
            'deleted' => false,
        ]);
    }

    public function actionDelete()
    {
        $deleted = false;
        $customerWebhooks = Webhook::find()
            ->where(['customer_id' => $this->shopifyApp->customer_id])
            ->all();

        foreach ($customerWebhooks as $webhook) {
            $webhookId = $webhook->shopify_webhook_id;
            $response = $this->shopify->rest('DELETE', 'admin/api/webhooks/' . $webhookId . '.json');
            // $webhook->delete();
           // $webhook->delete();
            $deleted = $webhook->delete();

            Yii::debug($response);
        }

        return $this->render('webhook', [
            'webhooks' => Webhook::find()->where(['customer_id' => $this->shopifyApp->customer_id])->all(),
            'deleted' => $deleted,
            'created' => false,
        ]);
    }
}





