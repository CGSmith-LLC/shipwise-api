<?php


namespace common\interfaces;


use yii\base\BaseObject;
use yii\helpers\Json;
use yii\httpclient\Client;

class ShopifyInterface extends BaseObject implements ECommerceInterface
{
    /**
     * @var Client $client
     */
    public Client $client;
    public string $auth;

    public const API_VERISON = "2021-04";
    public const BASE_SHOPIFY_URL = 'admin/api/' . self::API_VERISON . '/';


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
            } catch (\yii\base\InvalidArgumentException $e) {}

            $orders = end($orders);

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