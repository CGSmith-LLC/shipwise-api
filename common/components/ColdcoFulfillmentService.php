<?php


namespace common\components;


use common\models\Order;
use yii\httpclient\Client;

class ColdcoFulfillmentService extends \yii\base\Component
{

    public Client $client;

    final const ORDERS = 'orders';

    public $baseUrl;
    public $clientId;
    public $secret;
    public $request;

    public function init()
    {
        $this->client = new Client([
            'baseUrl' => $this->baseUrl,
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);
        $this->request = $this->client->createRequest();
        $this->generateNewToken();
        parent::init();
    }

    public function generateNewToken()
    {
        /**
         * We need to authenticate with 3PL
         * @link http://api.3plcentral.com/rels/auth
         */

        /**
        'threeplid' => 1137,
         */
        $key = '{30c84439-38cf-4ec7-b0eb-41e61172662b}';
        $authKey = base64_encode($this->clientId . ':' . $this->secret);
        $response = $this->client->createRequest()
            ->setMethod('POST')
            ->setUrl('AuthServer/api/Token')
            ->setHeaders(['Authorization' => 'Basic ' . $authKey])
            ->setData([
                'grant_type' => 'client_credentials',
                'user_login' => 'ShipWise',
            ])->send();
        var_dump($response);
        \Yii::debug($response);

    }

    public function getTracking(Order $order)
    {
        $id = null;
        $this->client->request(
            'GET',
            self::ORDERS . '/' . $id,
            [
                'headers' => ['Authorization' => "BEARER {$this->access_token}"],
                'query' => ['detail' => 'all']
            ]
        );
        $response = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl(self::ORDERS .'/'. $order->order_reference);

    }

}