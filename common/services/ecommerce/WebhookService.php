<?php

namespace common\services\ecommerce;

use common\exceptions\IgnoredWebhookException;
use common\models\IntegrationMeta;
use common\models\IntegrationWebhook;
use console\jobs\orders\CreateOrderJob;
use yii\httpclient\Client;

class WebhookService extends BaseEcommerceService
{
    /**
     * @var Client $client
     */
    public Client $client;
    public array $auth;

    /**
     * Meta data stored in IntegrationMeta
     */
    public $adapter;


    protected bool $canCreateWebhooks = true;


    /**
     * Ingest an array of the meta data for the service and apply to internal objects where needed
     *
     * @param array $metadata
     */
    public function applyMeta(array $metadata)
    {
        /**
         * @var IntegrationMeta $meta
         */
        foreach ($metadata as $meta) {
            $key = $meta->key;
            $this->$key = $meta->decryptedValue();
        }

        $this->prepareRequest();
    }

    public function prepareRequest()
    {

    }

    public function createWebhooks()
    {
        // Generate hookdeck integration
        $this->createHookdeck();

        $webhook = new IntegrationWebhook();
        $webhook->setAttributes([
            'integration_id' => $this->integration->id,
            'integration_hookdeck_id' => $this->hookdeck->id,
            'source_uuid' => rand(0,100),
            'name' => $this->hookdeck->destination_id,
            'topic' => 'Receiving',
        ]);
        $webhook->save();
    }

    /**
     * BigCommerce requires 3 calls to get an order from a webhook. It needs the order call, shipping_address, and
     * products call. I don't see a way around it.
     *
     * @param $unparsedOrder
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getFullOrderDataIfNecessary($unparsedOrder)
    {
        // if status ID matches then proceed
        // scope should be store/order/statusUpdated - status 11 (unfulfilled)
        if ($unparsedOrder['scope'] === 'store/order/statusUpdated' &&
            isset($unparsedOrder['data']['status']) &&
            $unparsedOrder['data']['status']['new_status_id'] == $this->statusId) {
            $orderURI = $this->storeHash . '/' . self::API_ORDERS . '/' . $unparsedOrder['data']['id'];

            // get order
            $response = $this->client->createRequest()
                ->setUrl($orderURI)
                ->setMethod('GET')
                ->send();
            $unparsedOrder = $response->getData();

            // get shipping addresses and attach to order
            $response = $this->client->createRequest()
                ->setUrl($orderURI . '/' . self::API_SHIPPING_ADDRESS)
                ->setMethod('GET')
                ->send();
            $unparsedOrder['shipping_addresses'] = $response->getData();

            // get products and attach to order
            $response = $this->client->createRequest()
                ->setUrl($orderURI . '/' . self::API_PRODUCTS)
                ->setMethod('GET')
                ->send();
            $unparsedOrder['products'] = $response->getData();

            return \Yii::$app->queue->push(new CreateOrderJob([
                'unparsedOrder' => $unparsedOrder,
                'integration_id' => $this->integration->id,
            ]));
        } else {
            throw new IgnoredWebhookException('This webhook is not monitored');
        }
    }
}