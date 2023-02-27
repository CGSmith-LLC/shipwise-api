# E-commerce Platforms

### How to connect a new platform:

1. Create a new `migration`.

2. Implement a new `insert` query for the table `ecommerce_platform`. Specify the needed platform. Example - `console\migrations\m230221_134343_add_shopify_mock_ecommerce_platform.php`.

3. Add implementation to:
- `common\models\EcommercePlatform.php`
- `common\models\EcommerceIntegration.php`
- `frontend\controllers\EcommercePlatformController.php`
- `frontend\controllers\EcommerceIntegrationController.php`

### How to manage existing platforms:

1. Visit our website - `/ecommerce-platform` (you must be an `Admin`).

# Shopify

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
