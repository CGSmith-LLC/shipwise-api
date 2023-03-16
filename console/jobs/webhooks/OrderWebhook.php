<?php

namespace console\jobs\webhooks;

use api\modules\v1\models\order\OrderEx;
use common\models\Webhook;
use common\models\WebhookLog;
use console\jobs\NotificationJob;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

class OrderWebhook extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    public $webhook_id;
    public $order_id;
    public Client $client;
    /** @var Webhook $webhook */
    public $webhook;
    public $testWebhook = false;
    public $order;
    public $headers = [];


    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        // All test webhook jobs to be sent even if they are inactive
        if ($this->testWebhook) {
            $this->webhook = Webhook::find()->where(['id' => $this->webhook_id])->one();
        } else {
            $this->webhook = Webhook::find()
                ->where(['id' => $this->webhook_id, 'active' => Webhook::STATUS_ACTIVE])
                ->one();
        }

        // Using API model so it sends the full request. Should match what we are sending from our API anyway
        // Fixes https://github.com/CGSmith-LLC/shipwise-api/issues/147
        $this->order = OrderEx::findOne($this->order_id);

        if (!$this->webhook) {
            throw new Exception(
                'Webhook does not exist anymore. Looks like this Order was queued while it 
                                existed or was active.'
            );
        }

        if (!$this->order) {
            throw new Exception('Order cannot be found. The order may have been deleted before the job could run');
        }

        try {
            $this->client = new Client([
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
                'parsers' => [
                    // configure options of the JsonParser, parse JSON as array
                    Client::FORMAT_JSON => [
                        'class' => 'yii\httpclient\JsonParser',
                        'asArray' => true,
                    ]
                ],
            ]);

            $this->addAuth();
            $this->signRequest();

            $response = $this->client->createRequest()
                ->setUrl($this->webhook->endpoint)
                ->setMethod('POST')
                ->addHeaders(['Accept' => 'application/json'])
                ->setOptions([
                    'followLocation' => false, // we don't follow redirects
                    'maxRedirects' => 0, // we don't allow redirects
                ])
                ->setData($this->order)
                ->send();

            $webhookLog = new WebhookLog();
            $webhookLog->webhook_id = $this->webhook->id;
            $webhookLog->status_code = $response->getStatusCode();
            $webhookLog->response = Json::encode($response->getData());
            $webhookLog->save();
        } catch (\Exception $e) {
            if (isset($response)) {
                $statusCode = $response->getStatusCode();
            } else {
                $statusCode = 500;
            }
            $webhookLog = new WebhookLog();
            $webhookLog->webhook_id = $this->webhook->id;
            $webhookLog->status_code = $statusCode;
            $webhookLog->response = $e->getMessage();
            $webhookLog->save();
            throw $e;
        }
    }

    /**
     * Sign request that is being made
     *
     * @return void
     */
    public function signRequest()
    {
        $webhook = $this->webhook;
        $order = $this->order;
        $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) use ($webhook, $order) {
            $signing = base64_encode(
                hash_hmac(
                    'sha256',
                    Json::encode($order),
                    $webhook->signing_secret,
                    true
                )
            );
            $event->request->addHeaders(['x-shipwise-signature' => $signing]);
        });
    }

    /**
     * Add authentication if chosen for basic auth or header auth
     *
     * @return void
     */
    public function addAuth()
    {
        if ($this->webhook->authentication_type == Webhook::BASIC_AUTH) {
            $encode = $this->webhook->user . ':' . $this->webhook->pass;
            $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) use ($encode) {
                $event->request->addHeaders(['Authorization' => 'Basic ' . base64_encode($encode)]);
            });
        } elseif ($this->webhook->authentication_type == Webhook::HEADER_AUTH) {
            $headerName = $this->webhook->user;
            $headerValue = $this->webhook->pass;
            $this->client->on(
                Request::EVENT_BEFORE_SEND,
                function (RequestEvent $event) use ($headerValue, $headerName) {
                    $event->request->addHeaders([$headerName => $headerValue]);
                }
            );
        }
    }


    public function getTtr()
    {
        return 3; // seconds
    }

    public function canRetry($attempt, $error)
    {
        if ($attempt < 3) {
            return true;
        } else {
            // not sure why but i cannot modify $this->webhook->active = 0; so I have to do it this way
            $webhook = Webhook::findOne($this->webhook_id);
            // Disable the webhook and send a notification to the user
            $webhook->active = Webhook::STATUS_INACTIVE;
            $webhook->save(false);
            \Yii::$app->queue->push(
                new NotificationJob([
                    'user_id' => $webhook->user_id,
                    'subject' => '🚨 Your webhook is failing: ' . $webhook->name,
                    'message' => 'This is possible if your endpoint isn\'t correct or is failing. <strong>We will disable it going forward.</strong>',
                    'url' => ['/webhook',],
                    'urlText' => 'See the failure message',
                ])
            );
        }
    }
}