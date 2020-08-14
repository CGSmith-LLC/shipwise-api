<?php

namespace shopify\controllers;


use common\models\Customer;
use common\models\CustomerMeta;
use common\models\Package;
use common\models\shopify\Shopify;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use PHPShopify\Shop;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Site controller
 */
class BaseController extends Controller
{
    public $customerMeta;
    public $code;
    public $accessToken;
    public $url;
    /** @var $shopify BasicShopifyAPI */
    public $shopify;
    public $shop; // Shopify URL after it's found
    public $scopes = [
        'write_orders',
        'read_orders',
        'read_locations',
        ];

    protected $shopifyApp;

    /**
     * @var bool Determines if the customer was just created or not
     */
    public $newCustomer = false;

    /**
     * @var string
     */
    private $api_key;
    /**
     * @var string
     */
    private $api_secret;

    const API_VERSION = '2020-04';
    /**
     * @var Options
     */
    protected $options;

    private function findShopifyAppOrCreate()
    {
        /**
         * Check if we already have a session or we already have an app available
         */
        if ($this->shop = Yii::$app->session->get('shopify-url')) {
            $this->shopifyApp = Shopify::find()->where(['shop' => $this->shop])->one();
        } else {
            $this->shop = Yii::$app->request->getQueryParam('shop', 'error');
        }

        /**
         * If shop fails throw an error
         */
        if ($this->shop === 'error') {
            throw new ServerErrorHttpException('No query parameter found for shop');
        }

        /**
         * If we don't have the shopify app, then try finding it from customer meta data
         */
        if (!$this->shopifyApp) {
            $customerMeta = CustomerMeta::find()->where([
                    'key' => 'shopify_store_url',
                    'value' => $this->shop]
            )->one();

            /**
             * Create customer if we don't meta data
             */
            if (!$customerMeta) {
                $this->newCustomer = true;
                $array = explode('.', $this->shop);
                $customer = new Customer([
                    'direct' => 1,
                    'name' => array_shift($array)
                ]);
                $customer->save();

                $customerMeta = new CustomerMeta([
                    'key' => 'shopify_store_url',
                    'value' => $this->shop,
                    'customer_id' => $customer->id,
                ]);
                $customerMeta->save();

                Yii::debug($customer);
            }

            /**
             * Create shopify app
             */
            $this->shopifyApp = new Shopify([
                'customer_id' => $customerMeta->customer_id,
                'shop' => $this->shop,
                'scopes' => implode(',',$this->scopes),
            ]);
            $this->shopifyApp->save();
            Yii::$app->session->set('shopify-url', $this->shop);
            //Check if we have a shopify URL and it matches in our App table\
        }

        if (empty($this->shopifyApp->access_token) && $this->module->requestedRoute !== 'site/callback') {
            $this->options = $this->getOptions();
            $api = new BasicShopifyAPI($this->options);
            $api->setSession(new Session($this->shop));

            /**
             * Redirect to shopify for access token
             */
            Yii::$app->response->redirect(
                $api->getAuthUrl($this->scopes, \yii\helpers\Url::toRoute(['/site/callback'], 'https'))
            );
            Yii::$app->end();
        }else {
            $this->options = $this->getOptions();
            $this->shopify = new BasicShopifyAPI($this->options);
            $this->shopify->setSession(new Session($this->shop, $this->shopifyApp->access_token));
        }

    }

    /**
     * @return Options
     * @throws \Exception
     */
    protected function getOptions()
    {
        $options = new Options();
        $options->setVersion(self::API_VERSION)
            ->setApiKey($this->api_key)
            ->setApiSecret($this->api_secret);
        return $options;
    }

    public function validateCallback()
    {
        $code = Yii::$app->request->getQueryParam('code', 'error');
        $shop = Yii::$app->request->getQueryParam('shop', 'error');

        /**
         * Validate $shop matches our session
         */
        if ($shop !== Yii::$app->session->get('shopify-url') ||
            $code === 'error'
        ) {
            throw new ServerErrorHttpException('Something went wrong with your request.');
        }

        $this->options = $this->getOptions();
        $this->shopify = new BasicShopifyAPI($this->options);
        // This needs to be after API
        if (!$this->shopify->verifyRequest(Yii::$app->request->getQueryParams())) {
            throw new ForbiddenHttpException('Authentication does not match');
        }

        $this->shopify->setSession(new Session($shop));
        $this->shopify->requestAndSetAccess($code);
        $this->shopifyApp->setAttribute('access_token', $this->shopify->getSession()->getAccessToken());

        return $this->shopifyApp->save();
    }

    public function init()
    {
        $this->api_key = Yii::$app->params['shopifyPublicKey'];
        $this->api_secret = Yii::$app->params['shopifyPrivateKey'];
        $this->findShopifyAppOrCreate();
    }

}





