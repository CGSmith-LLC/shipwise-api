# E-commerce Platforms

### How to connect a new platform:

1. Create a new `migration`.

2. Implement a new `insert` query for the table `ecommerce_platform`. Specify the needed platform. Example - `console\migrations\m230221_134343_add_shopify_mock_ecommerce_platform.php`.

3. Add implementation to:
- `common\models\EcommercePlatform`
- `common\models\EcommerceIntegration`
- `frontend\controllers\EcommercePlatformController`
- `frontend\controllers\EcommerceIntegrationController`

4. Create a Service similar to `common\services\platforms\ShopifyService`.

5. Create a Job similar to `console\jobs\platforms\ParseShopifyOrderJob`.

> php yii queue/listen --verbose

### Cron:

1. See `console\controllers\CronController.php` -> `runEcommerceIntegrations()`.
We need this method to pull existing orders from a needed E-commerce platform.
   
> php yii cron/frequent

### How to manage existing platforms:

1. Visit our website - URL: `/ecommerce-platform` (you must be an `Admin`).

# Constraints

1. A new integration (URL: `/ecommerce-integration`) can be added by a user only if the needed e-commerce platform
has the status `Active`.
   
2. Try to use `common\services\platforms\CreateOrderService` when you parse raw orders from E-commerce platforms.

3. In the cron method `runEcommerceIntegrations()`, only active (`status=connected`) E-commerce integrations are used for
order pulling.
   
4. Once we cannot pull orders from an E-commerce platform like Shopify, the E-commerce integration must become `uninstalled` automatically.
See `common\services\platforms\ShopifyService` -> `isTokenValid()` as an example. So we will not make any requests for the
E-commerce integration until the user reconnects the shop (URL: `/ecommerce-integration`).
   
5. Each E-commerce platform-integration can have specific user-based settings (access token, specific order statuses, etc.). 
For this, use the `meta` attribute (JSON) of the model `common\models\EcommerceIntegration`.

# E-commerce Integrations - Shopify

### App:

1. Register a new partner account - `https://www.shopify.com/partners`.

2. Go to `Apps` -> `Create app` -> `Create app manually`. Copy `Client ID` and `Client secret`. 
Insert them in `common\config\params-local.php` (`Shopify section`).
   
3. Go to `Apps` -> `App setup`. In the `URLs` section specify the parameters for `App URL` and `Allowed redirection URL(s)`.
If you're going to **test it locally**, specify:
   
- `App URL`: `https://shipwise.ngrok.io/`
- `Allowed redirection URL(s)`: `https://shipwise.ngrok.io/ecommerce-integration/shopify`

4. If you're going to **test it locally**, in `common\config\params-local.php` set the parameter `override_redirect_domain`
to `https://shipwise.ngrok.io`.
   
5. You need to request `Protected customer data access`. Go to `Apps` -> `Your app` -> `App setup` -> Find the section `Protected customer data access` ->
Press the button `Request access`. On the page, select and request access for:
   
- `Protected customer data`
- `Protected customer fields (optional)` -- all the fields
   
### Test shop(s):

1. Go to `https://partners.shopify.com/` -> `Stores`.

2. Press `Add store` -> `Create development store`.

3. Choose `Development store use`, specify `Store name`, specify `Store URL`.
In `Data and configurations`, choose `Start with test data`.
   
4. You can create several test shops if needed.

### Connect a test shop:

1. Visit our website - `/ecommerce-integration/index`. Press the button `Connect Shopify shop`.

2. In the form, specify your test shop's details (`name` and `URL`).

3. If you want to remove the Shopify app from your test shop, visit `https://admin.shopify.com/` -> `Apps` -> `Apps and sales channels` -> and press `Uninstall`.
