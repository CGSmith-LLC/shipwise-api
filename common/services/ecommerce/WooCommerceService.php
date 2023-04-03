<?php

namespace common\services\ecommerce;

use common\models\IntegrationHookdeck;
use common\models\IntegrationMeta;
use common\models\IntegrationWebhook;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

class WooCommerceService extends BaseEcommerceService
{
    /**
     * @var Client $client
     */
    public Client $client;
    public array $auth;

    /**
     * Meta data stored in IntegrationMeta
     */
    public $url;
    public $apiKey;
    public $apiPassword;
    public $orderStatus;
    protected bool $canCreateWebhooks = true;


    final public const API_BASE = '/wp-json/wc/v3';
    final public const API_ORDERS = 'orders';
    // @see https://woocommerce.github.io/woocommerce-rest-api-docs/#batch-update-webhooks
    final public const API_WEBHOOKS = 'webhooks/batch';

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
            'baseUrl' => $this->url,
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
            $event->request->addHeaders(['Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiPassword)]);
        });
    }

    public function createWebhooks()
    {
        // Generate hookdeck integration
        $this->createHookdeck();

        // Generate ecommerce webhooks
        $response = $this->client->createRequest()
            ->setUrl(self::API_BASE . '/' . self::API_WEBHOOKS)
            ->setMethod('POST')
            ->setData([
                'create' => [
                    [
                        'name' => 'Order Created -> Shipwise',
                        'topic' => 'order.created',
                        'delivery_url' => $this->hookdeck->source_url,
                        'secret' => $this->hookdeck->source_name // secret for HMAC verification
                    ], [
                        'name' => 'Order Updated -> Shipwise',
                        'topic' => 'order.updated',
                        'delivery_url' => $this->hookdeck->source_url,
                        'secret' => $this->hookdeck->source_name
                    ], [
                        'name' => 'Order Deleted -> Shipwise',
                        'topic' => 'order.deleted',
                        'delivery_url' => $this->hookdeck->source_url,
                        'secret' => $this->hookdeck->source_name
                    ],
                ]
            ])
            ->send();

        $webhookData = $response->getData();
        foreach ($webhookData['create'] as $webhookDatum) {
            $webhook = new IntegrationWebhook();
            $webhook->setAttributes([
                'integration_id' => $this->integration->id,
                'integration_hookdeck_id' => $this->hookdeck->id,
                'source_uuid' => $webhookDatum['id'],
                'name' => $webhookDatum['name'],
                'topic' => $webhookDatum['topic'],
            ]);
            $webhook->save();
        }

    }

    /**
     * Test the integration or throw an exception on why it is not working
     *
     * @return bool
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function testConnection()
    {
        $response = $this->client->createRequest()
            ->setUrl(self::API_BASE)
            ->setMethod('GET')
            ->send();

        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            $content = Json::decode($response->getContent());
            throw new Exception($content['message'], $response->getStatusCode());
        }
    }

    /**
     *
     * @see https://woocommerce.github.io/woocommerce-rest-api-docs/#list-all-orders
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getOrders()
    {
        if (is_null($this->last_success_run)) {
            $parameters = ['after' => $this->last_success_run]; //2017-03-22T16:28:02
        }
        $parameters = [
            'status' => $this->orderStatus, // default any
            'per_page' => $this->perPage, // default 10
            'page' => $this->page, // default 1
        ];

        return $this->client->createRequest()
            ->setUrl(self::API_BASE . '/' . self::API_ORDERS)
            ->setMethod('GET')
            ->setData($parameters)
            ->send();
    }

}