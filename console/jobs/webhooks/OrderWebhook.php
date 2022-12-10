<?php

namespace console\jobs\webhooks;

use Codeception\Lib\Interfaces\Web;
use common\models\Order;
use common\models\Webhook;
use common\models\WebhookLog;
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
    public $webhook;
    public $order;
    public $headers = [];


    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->webhook = Webhook::find()->where(['id' => $this->webhook_id, 'active' => Webhook::STATUS_ACTIVE])->one();
        $this->order = Order::findOne($this->order_id);

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
            $webhookLog = new WebhookLog();
            $webhookLog->webhook_id = $this->webhook->id;
            $webhookLog->status_code = $response->getStatusCode();
            $webhookLog->response = $e->getMessage();
            $webhookLog->save();
        }
    }


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
            $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) use ($headerValue, $headerName) {
                $event->request->addHeaders([$headerName => $headerValue]);
            });
        }
    }


    public function getTtr()
    {
        return 300; // seconds
    }

    public function canRetry($attempt, $error)
    {
        return ($attempt < 3);
    }
}