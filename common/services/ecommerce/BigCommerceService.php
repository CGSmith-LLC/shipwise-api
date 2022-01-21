<?php

namespace common\services\ecommerce;

use common\adapters\ecommerce\BigCommerceAdapter;
use common\exceptions\IgnoredWebhookException;
use common\exceptions\OrderExistsException;
use common\exceptions\WebhookExistsException;
use common\models\IntegrationMeta;
use common\models\IntegrationWebhook;
use common\models\Order;
use console\jobs\NotifierJob;
use console\jobs\orders\CancelOrderJob;
use console\jobs\orders\CreateOrderJob;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;

class BigCommerceService extends EcommerceService
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
    public const API_STATUSES = 'v2/order_statuses';
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
     * @TODO DELETE WEBHOOKS WHEN INTEGRATION SHUTS OFF AND STUFF
     */

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
        if ($unparsedOrder['scope'] !== 'store/order/statusUpdated' && !isset($unparsedOrder['data']['status'])) {
            throw new IgnoredWebhookException('This webhook is not monitored');
        }

        /**  if status ID matches then proceed
         * scope should be store/order/statusUpdated - status 11 (unfulfilled)
         * https://developer.bigcommerce.com/api-reference/store-management/orders/order-status/getorderstatus
         * See BigCommerceAdapter Constants
         */
        $status = $unparsedOrder['data']['status']['new_status_id'];
        if ($status == $this->statusId) {
            // check if order exists
            if (Order::find()
                ->where(['customer_reference' => (string)$unparsedOrder['data']['id']])
                ->andWhere(['customer_id' => $this->integration->customer_id])
                ->one()) {
                throw new OrderExistsException($unparsedOrder['data']['id']);
            }
            $orderURI = $this->storeHash . '/' . self::API_ORDERS . '/' . $unparsedOrder['data']['id'];

            // get order
            $response = $this->client->createRequest()->setUrl($orderURI)->setMethod('GET')->send();
            $unparsedOrder = $response->getData();

            // get shipping addresses and attach to order
            $response = $this->client->createRequest()->setUrl($orderURI . '/' . self::API_SHIPPING_ADDRESS)->setMethod('GET')->send();
            $unparsedOrder['shipping_addresses'] = $response->getData();

            // get products and attach to order
            $response = $this->client->createRequest()->setUrl($orderURI . '/' . self::API_PRODUCTS)->setMethod('GET')->send();
            $unparsedOrder['products'] = $response->getData();

            return \Yii::$app->queue->push(new CreateOrderJob([
                'unparsedOrder' => $unparsedOrder,
                'integration_id' => $this->integration->id,
            ]));
        } elseif ($this->isCancelOrderStatus($status)) {
            return \Yii::$app->queue->push(new CancelOrderJob([
                'customer_reference' => $unparsedOrder['data']['id'],
                'integration_id' => $this->integration->id,
            ]));
        } elseif ($this->notifyCustomerAboutCancel($status)) {
            if (Order::find()
                ->where(['customer_reference' => (string)$unparsedOrder['data']['id']])
                ->andWhere(['customer_id' => $this->integration->customer_id])
                ->one()) {
                // Not sure about this status - should let the customer decide
                // get status and inform customer in a well crafted message
                $response = $this->client->createRequest()->setUrl($this->storeHash . '/' . self::API_STATUSES . '/' . $status)->setMethod('GET')->send();
                $data = $response->getData(); // $data['custom_label']; // 'Refunded'  or w/e the customer names it

                return \Yii::$app->queue->push(new NotifierJob([
                    'message' => 'We do not monitor the status of "' . $data['custom_label'] . '". Manual action may be required by you.',
                    'customer_reference' => $unparsedOrder['data']['id'],
                    'customer_id' => $this->integration->customer_id,
                    'reason_general' => 'an order',
                    'reason_specific' => 'Order #' . $unparsedOrder['data']['id'],
                ]));
            }
        } else {
            throw new IgnoredWebhookException('This webhook is not monitored');
        }
    }

    /**
     * Return true if we should attempt to update the order downstream
     * @param int $status
     * @return bool
     */
    public function isCancelOrderStatus($status = 0): bool
    {
        return match ($status) {
            BigCommerceAdapter::STATUS_INCOMPLETE,
            BigCommerceAdapter::STATUS_PENDING,
            BigCommerceAdapter::STATUS_CANCELLED,
            BigCommerceAdapter::STATUS_DECLINED,
            BigCommerceAdapter::STATUS_AWAITING_PAYMENT,
            BigCommerceAdapter::STATUS_MANUAL_VERIFICATION_REQUIRED,
            BigCommerceAdapter::STATUS_DISPUTED => true,
            default => false,
        };
    }

    /**
     * Return true if we should notify the customer
     * @param int $status
     * @return bool
     */
    public function notifyCustomerAboutCancel($status = 0): bool
    {
        return match ($status) {
            BigCommerceAdapter::STATUS_REFUNDED,
            BigCommerceAdapter::STATUS_PARTIALLY_SHIPPED,
            BigCommerceAdapter::STATUS_PARTIALLY_REFUNDED => true,
            default => false,
        };
    }
}