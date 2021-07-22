<?php


namespace common\services;


use common\models\IntegrationMeta;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;

class ShopifyService extends BaseService
{
    /**
     * @var Client $client
     */
    public Client $client;
    public string $auth;

    /**
     * Meta data stored in IntegrationMeta
     */
    public const META_URL = 'url';
    public const META_API_KEY = 'api_key';
    public const META_API_SECRET = 'api_secret';

    public const API_VERISON = "2021-04";
    public const BASE_SHOPIFY_URL = 'admin/api/' . self::API_VERISON . '/';

    /**
     * Ingest an array of the meta data for the service and apply to internal objects where needed
     *
     * @todo Change function name to buildComponents???
     * @param array $integrationMeta
     */
    public function applyMeta(array $integrationMeta)
    {
        // init variable
        $auth = [];

        /**
         * @var IntegrationMeta $meta
         */
        foreach ($integrationMeta as $meta) {
            if ($meta->key === self::META_URL) {
                $this->client = new Client(['baseUrl' => $meta->decryptedValue()]);
            }
            if ($meta->key === self::META_API_KEY) {
                $auth[0] = $meta->decryptedValue();
            }
            if ($meta->key === self::META_API_SECRET) {
                $auth[1] = $meta->decryptedValue();
            }
        }

        // add semicolon to end for BASIC auth
        $this->auth = implode(':', $auth);

    }

    public function getOrders(): array
    {
        $startDate = new \DateTime('-12 minutes', new \DateTimeZone('America/Chicago'));

        $orderarray = [];

        $page = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl(self::BASE_SHOPIFY_URL . 'orders.json')
            ->setData([
                'created_at_min' => $startDate->format(DATE_ISO8601),
                'status' => 'open',
                'fulfillment_status' => null,
                'limit' => 250,
            ])->setHeaders(['Authorization' => "Basic {$this->auth}"])
            ->send();

        do {
            $pagesLeft = false;

            //  Add page to $orderarray
            $orders = $page->getContent();
            $headers = $page->getHeaders();

            try {
                $orders = Json::decode($orders, true);
            } catch (\yii\base\InvalidArgumentException $e) {
                $orders = ['error' => "Error on decode: $e"];
            }

            $orders = end($orders);

            if(!is_array($orders)){
                throw new Exception('$orders is not an array. Orders reads: ' . $orders);
            }

            foreach($orders as $order)
            {
                $order = Json::encode($order);
                $orderarray[] = $order;
            }

            if (isset($headers['link'])) {
                $pageLinks = explode(', ', $headers['link']);
                $nextPageLink = preg_grep("/rel=\"next\"$/", $pageLinks);
                $nextPageLink = end($nextPageLink);
                $nextPageLink = substr(
                    substr(
                        $nextPageLink, 1, strlen($nextPageLink)
                    ), 0, strpos($nextPageLink, '>;') - 1
                );

                $pagesLeft = true;

                $page = $this->client->createRequest()
                    ->setMethod('GET')
                    ->setUrl($nextPageLink)
                    ->setHeaders(['Authorization' => "Basic {$this->auth}"])
                    ->send();
            }
        } while ($pagesLeft);

        /**
         * 1. Get all unfulfilled Shopify orders from the last 12 minutes (just to be safe)
         * 2. Extract all individual order object-arrays from array
         */

        return $orderarray;
    }
}