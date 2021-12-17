<?php

namespace common\services\ecommerce;

use common\exceptions\IgnoredWebhookException;
use common\exceptions\WebhookExistsException;
use common\models\IntegrationMeta;
use common\models\IntegrationWebhook;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

class BigCommerceService extends BaseEcommerceService
{
    /**
     * @var Client $client
     */
    public Client $client;
    public array $auth;

    /**
     * Meta data stored in IntegrationMeta
     */
    public $storeHash;
    public $accessToken;
    public $clientId;
    public $clientSecret;
    public $statusId;
    protected bool $canCreateWebhooks = true;


    public const API_BASEURL = 'https://api.bigcommerce.com/stores/';
    public const API_ORDERS = 'v2/orders';
    public const API_SHIPPING_ADDRESS = 'shipping_addresses';
    public const API_PRODUCTS = 'products';
    // @see https://developer.bigcommerce.com/api-docs/store-management/webhooks/overview
    public const API_WEBHOOKS = 'v3/hooks';

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
        $this->client = new Client([
            'baseUrl' => self::API_BASEURL,
            'requestConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'parsers' => [
                // configure options of the JsonParser, parse JSON as objects
                Client::FORMAT_JSON => [
                    'class' => 'yii\httpclient\JsonParser',
                    'asArray' => true,
                ]
            ],
        ]);

        // Setup event for auth before each send
        $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
            $event->request->addHeaders(['X-Auth-Token' => $this->accessToken]);
            $event->request->addHeaders(['Accept' => 'application/json']);
        });
    }

    public function createWebhooks()
    {
        // Generate hookdeck integration
        $this->createHookdeck();

        // Generate ecommerce webhooks
        $response = $this->client->createRequest()
            ->setUrl($this->storeHash . '/' . self::API_WEBHOOKS)
            ->setMethod('POST')
            ->setData([
                'scope' => 'store/order/*',
                'destination' => $this->hookdeck->source_url,
                'is_active' => true
            ])
            ->send();

        // Save webhook that was created
        $webhookDatum = $response->getData();

        if ($webhookDatum['status'] == 422) {
            throw new WebhookExistsException('BigCommerce webhook already exists', 422);
        }

        $webhook = new IntegrationWebhook();
        $webhook->setAttributes([
            'integration_id' => $this->integration->id,
            'integration_hookdeck_id' => $this->hookdeck->id,
            'source_uuid' => $webhookDatum['data']['id'],
            'name' => $webhookDatum['data']['id'],
            'topic' => $webhookDatum['data']['scope'],
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

            return $unparsedOrder;
        } else {
            throw new IgnoredWebhookException('This webhook is not monitored');
        }
    }
}