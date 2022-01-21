<?php

namespace common\services\ecommerce;

use common\adapters\fulfillment\ThreePLCentral;
use common\exceptions\OrderCancelException;
use common\models\IntegrationMeta;
use common\models\Order;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Request;
use yii\httpclient\RequestEvent;
use yii\web\UnauthorizedHttpException;

class AmericoldService extends BaseService
{
    /**
     * @var Client $client
     */
    public Client $client;

    const STL_FACILITY_ID = 1;
    const RENO_FACILITY_ID = 2;
    const EAST_FACILITY_ID = 3;

    /**
     * Meta data stored in IntegrationMeta
     */
    public $customerId = 0;

    public const API_BASEURL = 'https://secure-wms.com/';
    public const API_AUTH = 'AuthServer/api/Token';
    public const API_ORDERS = 'orders';

    /** @var string $clientid Client ID for 3PL Central */
    private string $clientid;

    /** @var string $clientsecret Client Secret for 3PL Central */
    private string $clientsecret;

    /** @var string $user_login User name for 3PL Central Login */
    private string $user_login;

    /** @var string $accessToken generated access token that we can cache for 1 hour */
    private string $accessToken;
    private \yii\httpclient\Response $response;

    /**
     * Initialize object with params specified in params-local.php
     */
    public function init()
    {
        // Get local params for auth
        $this->clientid = \Yii::$app->params['americold']['clientid'];
        $this->clientsecret = \Yii::$app->params['americold']['clientsecret'];
        $this->user_login = \Yii::$app->params['americold']['user_login'];;

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

        // If token is not available then authenticate with the service first
        $this->accessToken = \Yii::$app->cache->getOrSet(__CLASS__, function () {
            return $this->authenticate();
        }, 3600);
    }

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

        if (!$this->customerId) {
            throw new InvalidConfigException('customerId is required in service request which should be set by meta data');
        }

        $this->prepareRequest();
    }

    /**
     * This runs before each request (except auth) and is sent down before the client sends a command
     */
    public function prepareRequest()
    {
        // Setup event for auth before each send
        $this->client->on(Request::EVENT_BEFORE_SEND, function (RequestEvent $event) {
            $event->request->addHeaders(['Authorization' => "BEARER {$this->accessToken}"]);
            $event->request->addHeaders(['Accept' => 'application/json']);
        });
    }

    /**
     * @throws \yii\httpclient\Exception
     * @throws UnauthorizedHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function authenticate()
    {
        $response = $this->client->createRequest()
            ->setUrl(self::API_AUTH)
            ->setMethod('POST')
            ->addHeaders(['Authorization' => 'Basic ' . base64_encode($this->clientid . ':' . $this->clientsecret)])
            ->setData([
                'grant_type' => 'client_credentials',
                'user_login' => $this->user_login,
            ])
            ->send();

        // Save webhook that was created
        if ($response->isOk) {
            return $response->data['access_token'];
        } else {
            throw new UnauthorizedHttpException($response->data);
        }
    }

    /**
     * Get specific WMS order
     *
     * @return AmericoldService
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\httpclient\Exception
     */
    public function getOrder($id)
    {
        $this->response = $this->client->get(self::API_ORDERS . '/' . $id . '?detail=All', ['query_string' => ['detail' => 'All']])->send();

        return $this;
    }

    public function getOrderByCustomerReference($customerReference)
    {
        $this->response = $this->client->createRequest()
            ->setUrl(self::API_ORDERS . "?rql=ReferenceNum=={$customerReference};Readonly.CustomerIdentifier.Id=={$this->customerId}")
            ->send();

        return $this;
    }

    public function cancelOrder(Order $order)
    {
        if (isset($order->order_reference)) {
            $threePLOrder = $this->getOrder($order->order_reference)->parseOrder();
        } else {
            $this->getOrderByCustomerReference($order->customer_reference);
            if ($this->response->isOk) {
                $threePLOrder = $this->getOrder($this->response->data['ResourceList'][0]['ReadOnly']['OrderId'])->parseOrder();
            }
        }

        if ($threePLOrder) {
            var_dump($threePLOrder);
            echo 'Cancelling';
            return;
        }

        throw new OrderCancelException();
    }

    public function parseOrder()
    {
        $order = new ThreePLCentral();
        var_dump($this->response->data['ReadOnly']);die;
        $order->load([
            'customerIdentifier' => $this->response->data['ReadOnly']['CustomerIdentifier']['Id'],
            'facilityIdentifier' => $this->response->data['ReadOnly']['FacilityIdentifier']['Id'],
            'referenceNum' => $this->response->data['ReadOnly']['OrderId'],
            'eTag' => $this->response->headers->get('etag'),
            'carrier' => $this->response->data['ReadOnly']['RoutingInfo']['Carrier'],
            'carrierMode' => $this->response->data['ReadOnly']['RoutingInfo']['Mode'],
            'carrierAccount' => $this->response->data['ReadOnly']['RoutingInfo']['Account'],
            'billingCode' => $this->response->data['ReadOnly']['BillingCode'],
            'earliestShipDate' => $this->response->data['ReadOnly']['EarliestShipDate'],
            'shipCancelDate' => $this->response->data['ReadOnly']['ShipCancelDate'],
            'shippingNotes' => $this->response->data['ReadOnly']['ShippingNotes'],
            'notes' => $this->response->data['ReadOnly']['Notes'],
            'shipToCompany' => $this->response->data['ReadOnly']['ShipTo']['CompanyName'],
            'shipToName' => $this->response->data['ReadOnly']['ShipTo']['Name'],
            'shipToAddress1' => $this->response->data['ReadOnly']['ShipTo']['Address1'],
            'shipToAddress2' => $this->response->data['ReadOnly']['ShipTo']['Address2'],
            'shipToCity' => $this->response->data['ReadOnly']['ShipTo']['City'],
            'shipToState' => $this->response->data['ReadOnly']['ShipTo']['State'],
            'shipToZip' => $this->response->data['ReadOnly']['ShipTo']['Zip'],
            'shipToCountry' => $this->response->data['ReadOnly']['ShipTo']['Country'],
            'shipToPhone' => $this->response->data['ReadOnly']['ShipTo']['PhoneNumber'],
        ], '');

        return $order;
    }
}