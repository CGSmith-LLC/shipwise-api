<?php


namespace common\services\ecommerce;


use common\models\IntegrationMeta;
use yii\base\BaseObject;
use yii\console\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;

class ShopifyService extends BaseEcommerceService
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
     * @param array $metadata
     *@todo Change function name to buildComponents???
     */
    public function applyMeta(array $metadata)
    {
        // init variable
        $auth = [];

        /**
         * @var IntegrationMeta $meta
         */
        foreach ($metadata as $meta) {
        	switch($meta->key)
			{
				case self::META_URL:
					$this->client = new Client(['baseUrl' => $meta->decryptedValue()]);
					break;
				case self::META_API_KEY:
					$auth[0] = $meta->decryptedValue();
					break;
				case self::META_API_SECRET:
					$auth[1] = $meta->decryptedValue();
					break;
			}
        }

        // add semicolon to end for BASIC auth
        $this->auth = base64_encode(implode(array: $auth, separator: ':'));
    }

    public function getOrders(): array
    {
        $startDate = new \DateTime('-12 minutes', new \DateTimeZone('America/Chicago'));

        $orderarray = [];

        $page = $this->client->createRequest()
            ->setMethod(method: 'GET')
            ->setUrl(url: self::BASE_SHOPIFY_URL . 'orders.json')
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

            $orders = end(array:$orders);

            if(!is_array($orders)){
                throw new Exception(message: '$orders is not an array. Orders reads: ' . $orders);
            }

            foreach($orders as $order)
            {
                $order = Json::encode($order);
                $orderarray[] = $order;
            }

            if (isset($headers['link'])) {
                $pageLinks = explode(string: $headers['link'], separator: ', ');
                $nextPageLink = preg_grep(pattern:"/rel=\"next\"$/", array: $pageLinks);
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