<?php

namespace common\services\platforms;

use Yii;
use common\models\EcommerceIntegration;
use common\models\EcommercePlatform;
use PHPShopify\ShopifySDK;
use PHPShopify\AuthHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

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
    protected ?string $token = null;
    protected string $shopUrl;
    protected string $scopes = 'read_products,read_customers,read_fulfillments,read_orders,read_shipping,read_returns';
    protected string $redirectUrl = '/ecommerce-integration/shopify';
    protected ShopifySDK $shopify;

    public function __construct(string $shopUrl, string $token = null)
    {
        $this->shopUrl = $shopUrl;
        $this->token = $token;

        if (!$this->token) { // Authorize user's shop
            $config = [
                'ShopUrl' => $this->shopUrl,
                'ApiKey' => Yii::$app->params['shopify']['client_id'],
                'SharedSecret' => Yii::$app->params['shopify']['client_secret'],
            ];
        } else { // Use existing user's shop
            $config = [
                'ShopUrl' => $this->shopUrl,
                'AccessToken' => $this->token,
            ];
        }

        $this->shopify = new ShopifySDK($config);
    }

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

    public function accessToken(string $shopName)
    {
        $accessToken = AuthHelper::createAuthRequest($this->scopes);

        $meta = [
            'platform' => EcommercePlatform::SHOPIFY_PLATFORM_NAME,
            'shop_url' => $this->shopUrl,
            'shop_name' => $shopName,
            'access_token' => $accessToken,
        ];

        $ecommerceIntegration = new EcommerceIntegration();
        $ecommerceIntegration->user_id = Yii::$app->user->id;
        $ecommerceIntegration->platform_id = EcommercePlatform::findOne(['name' => EcommercePlatform::SHOPIFY_PLATFORM_NAME])->id;
        $ecommerceIntegration->status = EcommerceIntegration::STATUS_INTEGRATION_CONNECTED;
        $ecommerceIntegration->meta = Json::encode($meta, JSON_PRETTY_PRINT);

        if (!$ecommerceIntegration->save()) {
            throw new ServerErrorHttpException('Shopify integration is not added. Something went wrong.');
        }
    }

    public function makeReq()
    {
        echo '<pre>';
        print_r($this->shopify->Product->get());
        exit;
    }
}
