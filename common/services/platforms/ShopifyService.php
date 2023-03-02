<?php

namespace common\services\platforms;

use Yii;
use common\models\Order;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Url;
use console\jobs\platforms\ParseShopifyOrderJob;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;
use PHPShopify\ShopifySDK;
use PHPShopify\AuthHelper;
use yii\web\ServerErrorHttpException;
use PHPShopify\Exception\SdkException;

/**
 * Class ShopifyService
 * @package common\services\platforms
 *
 * @see https://partners.shopify.com/
 * @see https://shopify.dev/docs/apps/auth/oauth/getting-started
 * @see https://shopify.dev/docs/api/usage/access-scopes
 * @see https://shopify.dev/docs/apps/webhooks
 * @see https://shopify.dev/docs/apps/webhooks/configuration
 * @see https://github.com/phpclassic/php-shopify
 * @see https://community.shopify.com/c/shopify-apis-and-sdks/will-access-token-expired/td-p/559870
 */
class ShopifyService
{
    protected const API_VERSION = '2023-01';
    protected string $shopUrl;
    protected string $scopes = 'read_products,read_customers,read_fulfillments,read_orders,read_shipping,read_returns';
    protected string $redirectUrl = '/ecommerce-integration/shopify';
    protected ShopifySDK $shopify;
    protected ?EcommerceIntegration $ecommerceIntegration = null;

    /**
     * @throws InvalidConfigException
     */
    public function __construct(string $shopUrl, EcommerceIntegration $ecommerceIntegration = null)
    {
        $this->shopUrl = $shopUrl;
        $this->ecommerceIntegration = $ecommerceIntegration;
        $config = [
            'ApiVersion' => self::API_VERSION,
            'ShopUrl' => $this->shopUrl,
        ];

        if (!$this->ecommerceIntegration) { // Authorize user's shop:
            $config['ApiKey'] = Yii::$app->params['shopify']['client_id'];
            $config['SharedSecret'] = Yii::$app->params['shopify']['client_secret'];
        } else { // Use existing user's shop:
            $config['AccessToken'] = $this->ecommerceIntegration->array_meta_data['access_token'];
        }

        $this->shopify = new ShopifySDK($config);

        // Check if the provided token is valid:
        if ($this->ecommerceIntegration) {
            $this->isTokenValid();
        }
    }

    #########
    # Auth: #
    #########

    /**
     * @throws SdkException
     */
    public function auth(): void
    {
        $redirectDomain = trim(Url::to(['/'], true), '/');

        if (Yii::$app->params['shopify']['override_redirect_domain'] != false) {
            $redirectDomain = Yii::$app->params['shopify']['override_redirect_domain'];
        }

        // Step 1 - Send request to receive access token:
        AuthHelper::createAuthRequest($this->scopes, $redirectDomain . $this->redirectUrl);
        exit;
    }

    /**
     * @throws SdkException
     * @throws ServerErrorHttpException
     */
    public function accessToken(string $shopName, int $userId, int $customerId): void
    {
        // Step 2 - Receive and save access token:
        $accessToken = AuthHelper::createAuthRequest($this->scopes);

        $meta = [
            'platform' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'shop_url' => $this->shopUrl,
            'shop_name' => $shopName,
            'access_token' => $accessToken,
        ];

        $ecommerceIntegration = new EcommerceIntegration();
        $ecommerceIntegration->user_id = $userId;
        $ecommerceIntegration->customer_id = $customerId;
        $ecommerceIntegration->platform_id = EcommercePlatform::findOne(['name' => EcommercePlatform::SHOPIFY_PLATFORM_NAME])->id;
        $ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_CONNECTED;
        $ecommerceIntegration->meta = Json::encode($meta, JSON_PRETTY_PRINT);

        if (!$ecommerceIntegration->save()) {
            throw new ServerErrorHttpException('Shopify integration is not added. Something went wrong.');
        }
    }

    /**
     * @throws InvalidConfigException
     */
    protected function isTokenValid()
    {
        try {
            $this->getProductsList();
        } catch (\Exception $e) {
            throw new InvalidConfigException('Shopify token for the shop `' . $this->shopUrl . '` is invalid.');
        }
    }

    #####################
    # Get data via API: #
    #####################

    public function getProductsList(): array
    {
        return $this->shopify->Product->get();
    }

    public function getProductById(int $id): array
    {
        return $this->shopify->Product($id)->get();
    }

    public function getOrdersList(array $params = []): array
    {
        return $this->shopify->Order->get($params);
    }

    public function getOrderById(int $id): array
    {
        return $this->shopify->Order($id)->get();
    }

    public function getCustomerById(int $id): array
    {
        return $this->shopify->Customer($id)->get();
    }

    public function getCustomerAddressById(int $customerId, int $addressId): array
    {
        return $this->shopify->Customer($customerId)->Address($addressId)->get();
    }

    ##################
    # Order parsing: #
    ##################

    public function parseRawOrderJob(array $order): void
    {
        if ($this->canBeParsed($order) && $this->isNotDuplicate($order)) {
            Yii::$app->queue->push(
                new ParseShopifyOrderJob([
                    'rawOrder' => $order,
                    'ecommerceIntegrationId' => $this->ecommerceIntegration->id
                ])
            );
        }
    }

    protected function canBeParsed(array $order): bool
    {
        return (isset($order['shipping_address']) && isset($order['customer']));
    }

    protected function isNotDuplicate(array $order): bool
    {
        return !Order::find()->where([
            'origin' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'order_reference' => $order['name'],
            'customer_reference' => $order['id'],
            'customer_id' => $this->ecommerceIntegration->customer_id,
        ])->exists();
    }
}
