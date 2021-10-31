<?php

namespace common\services\ecommerce;

use common\models\IntegrationMeta;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;

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

    public const API_VERSION = "2021-04";
    public const BASE_WOOCOMMERCE_URL = 'admin/api/' . self::API_VERSION . '/';

    /**
     * Ingest an array of the meta data for the service and apply to internal objects where needed
     *
     * @param array $metadata
     * @todo Change function name to buildComponents???
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
        $this->client = new Client(['baseUrl' => $this->url]);
        $this->auth = ['Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->apiPassword)];
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
            ->setUrl('/wp-json/wc/v3')
            ->setMethod('GET')
            ->setHeaders($this->auth)
            ->send();

        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            $content = Json::decode($response->getContent());
            throw new Exception($content['message'], $response->getStatusCode());
        }
    }

    public function getOrders(): array
    {
        $startDate = new \DateTime('-12 minutes', new \DateTimeZone('America/Chicago'));
        $orderarray = [];
        $page = $this->client->createRequest()
            ->setMethod(method: 'GET')
            ->setUrl(url: self::BASE_WOOCOMMERCE_URL . 'orders.json')
            ->setData([
                'created_at_min' => $startDate->format(format: DATE_ISO8601),
                'status' => 'open',
                'fulfillment_status' => null,
                'limit' => 250,
            ])->setHeaders(['Authorization' => "Basic {$this->auth}"])
            ->send();
        $pages = 1;

        do {
            $pagesLeft = false;

            //  Add page to $orderarray
            $orders = $page->getContent();
            $headers = $page->getHeaders();

            try {
                $orders = Json::decode($orders, asArray: true);
            } catch (\yii\base\InvalidArgumentException $e) {
                $orders = ['error' => "Error on decode: $e"];
            }

            $orders = end(array: $orders);

            if (!is_array($orders)) {
                throw new Exception(message: '$orders is not an array. Orders reads: ' . $orders);
            }

            foreach ($orders as $order) {
                $order = Json::encode($order);
                $orderarray[] = $order;
            }

            if (isset($headers['link'])) {
                $pageLinks = explode(string: $headers['link'], separator: ', ');
                $nextPageLink = preg_grep(pattern: "/rel=\"next\"$/", array: $pageLinks);
                $nextPageLink = end(array: $nextPageLink);
                $nextPageLink = substr(
                    string: substr(
                    string: $nextPageLink, offset: 1, length: strlen($nextPageLink)
                ), offset: 0, length: strpos($nextPageLink, '>;') - 1
                );

                $pagesLeft = true;
                $pages++;

                $page = $this->client->createRequest()
                    ->setMethod(method: 'GET')
                    ->setUrl($nextPageLink)
                    ->setHeaders(['Authorization' => "Basic {$this->auth}"])
                    ->send();
            }
        } while ($pagesLeft);

        /**
         * 1. Get all unfulfilled Shopify orders from the last 12 minutes (just to be safe)
         * 2. Extract all individual order object-arrays from array
         */

        echo "\t$pages page(s) of orders. " . (count($orderarray)) . " order(s) found" . PHP_EOL;

        return $orderarray;

    }
}