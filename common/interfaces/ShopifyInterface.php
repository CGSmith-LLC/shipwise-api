<?php


namespace common\interfaces;


use phpDocumentor\Reflection\Types\String_;
use yii\base\BaseObject;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use function GuzzleHttp\Psr7\str;

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
        $startDate = new \DateTime('-11 days', new \DateTimeZone('America/Chicago'));

        $orderarray = [];

        $page = $this->client->createRequest()
            ->setMethod('GET')
            ->setUrl(self::BASE_SHOPIFY_URL . 'orders.json')
            ->setData([
                'created_at_min' => $startDate->format(DATE_ISO8601),
                'status' => 'open',
                'fulfillment_status' => null,
                'limit' => 25,
            ])->setHeaders(['Authorization' => "Basic {$this->auth}"])
            ->send();

        do {
            $pagesLeft = false;

            //  Add page to $orderarray
            $orders = $page->getContent();
            $headers = $page->getHeaders();

            var_dump($headers);
            if (isset($headers['link'])) {
                echo 'preProcessing ';

                $pageLinks = explode(',', $headers['link']);
                $nextPageLink = preg_grep("/rel=\"next\"$/", $pageLinks);
                $nextPageLink = end($nextPageLink);
                $nextPageLink = substr(
                    substr(
                       $nextPageLink, 1, strlen($nextPageLink)
                    ), 0, strpos($nextPageLink, '>;')-1
                );

                $pagesLeft = true;

                echo 'preNewPage ';

                $page = $this->client->createRequest()
                    ->setMethod('GET')
                    ->setUrl($nextPageLink)
                    ->setHeaders(['Authorization' => "Basic {$this->auth}"])
                    ->send();
            }
            echo 'endWhile'.PHP_EOL;
        } while ($pagesLeft);

        /**
         * 1. Get all unfulfilled Shopify orders from the last 11 minutes (just to be safe)
         * 2. Extract all individual order object-arrays from array
         */

        return $orderarray;
    }
}