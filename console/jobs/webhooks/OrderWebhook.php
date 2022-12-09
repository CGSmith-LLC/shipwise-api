<?php

namespace console\jobs\webhooks;

use common\models\Order;
use common\models\Webhook;
use yii\httpclient\Client;

class OrderWebhook extends \yii\base\BaseObject implements \yii\queue\RetryableJobInterface
{

    public $webhook_id;
    public $order_id;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $webhook = Webhook::findOne($this->webhook_id);
        $order = Order::findOne($this->order_id);

        /*
         * $this->client->createRequest()
            ->setUrl($this->storeHash . '/' . self::API_WEBHOOKS)
            ->setMethod('POST')
            ->setData([
                'scope' => 'store/order/*',
                'destination' => $this->hookdeck->source_url,
                'is_active' => true
            ])
            ->send();
         */
        try {
            $client = new Client([
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON
                ],
            ]);
            $client->createRequest()
                ->setUrl($webhook->endpoint)
                ->setMethod('POST')
                ->setData($order)
                ->send();
        } catch (\Exception $e) {
            echo $e->getMessage();
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