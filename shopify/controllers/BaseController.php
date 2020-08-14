<?php

namespace shopify\controllers;


use common\models\Customer;
use common\models\CustomerMeta;
use common\models\shopify\Shopify;
use Osiset\BasicShopifyAPI\BasicShopifyAPI;
use Osiset\BasicShopifyAPI\Options;
use Osiset\BasicShopifyAPI\Session;
use PHPShopify\Shop;
use Yii;
use yii\web\Controller;
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

    public $newCustomer = false;

    private function findShopifyAppOrCreate()
    {
        /**
         * Check if we already have a session or we already have an app available
         */
        if ($this->shop = Yii::$app->session->get('shopify-url')) {
            $shopifyApp = Shopify::find()->where(['shop' => $this->shop])->one();
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
        if (!$shopifyApp) {
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

            //Check if we have a customer meta data field that matches the store URL
            $shopifyApp = new Shopify([
                'customer_id' => $customerMeta->customer_id,
                'shop' => $this->shop,
                'scopes' => implode(',',$this->scopes),
            ]);
            $shopifyApp->save();


            //Check if we have a shopify URL and it matches in our App table

        }

    }

    public function init()
    {
        $this->findShopifyAppOrCreate();


        // hmac=b39f98818f3f6cf64576900506f8cb2ed66f2ceb89e90cf592af8a97247b3bca
        // shop=cgsmith105.myshopify.com
        // timestamp=1597082650
        if (!Yii::$app->session->get('shopify-code') || Yii::$app->request->getQueryParam('hmac')) {

            /**
             * TODO: Validate hmac with timestamp
             */
            $redirect = \yii\helpers\Url::toRoute(['site/callback'], 'https');
            $scopes = ['write_orders'];


            if ($shopUrl === 'error') {
                throw new ServerErrorHttpException('Missing shop parameter');
            }

            /**
             * 1. configure options
             * 2. call api with store
             */
            $options = new Options();
            $options->setVersion('2020-04'); // TODO chang ethis in the config
            $options->setApiKey(Yii::$app->params['shopifyPublicKey']);
            $options->setApiSecret(Yii::$app->params['shopifyPrivateKey']);
            $api = new BasicShopifyAPI($options);
            $api->setSession(new Session($shopUrl));

            $redirectUrl = $api->getAuthUrl($scopes, $redirect);

            Yii::$app->response->redirect($redirectUrl);
            Yii::$app->end();
        }
        $this->code = Yii::$app->session->get('shopify-code');
        $this->url = Yii::$app->session->get('shopify-url');
        $options = new Options();
        $options->setVersion('2020-04');
        $options->setApiKey(Yii::$app->params['shopifyPublicKey']);
        $options->setApiSecret(Yii::$app->params['shopifyPrivateKey']);
        $this->shopify = new BasicShopifyAPI($options);
        $this->shopify->setSession(new Session($this->url));
        $this->shopify->requestAndSetAccess($this->code);
    }

    public function actionCallback()
    {
        // code=d7b8d8cd7974e15f03bfc64c2931f3a3
        // hmac=788397aa915e41996a698e97cad29db7209e566dc04b963fd3d586c69b64e00d
        // shop=cgsmith105.myshopify.com
        // timestamp=1597083500

        $code = Yii::$app->request->getQueryParam('code', 'error');

        if ($code === 'error') {
            throw new ServerErrorHttpException('Code not valid');
        }

        Yii::$app->session->set('shopify-code', $code);
        Yii::$app->session->set('shopify-url', Yii::$app->request->getQueryParam('shop'));
        $this->redirect(['site/index']);
    }
}





