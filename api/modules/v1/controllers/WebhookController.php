<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\core\ApiConsumerEx;
use api\modules\v1\models\mappers\ShopifyMapper;
use common\models\ApiConsumer;
use common\models\CustomerMeta;
use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class WebhookController
 *
 * @package api\modules\v1\controllers
 *
 */
class WebhookController extends ControllerEx
{

    /** @inheritdoc */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST'],
            'shopify' => ['POST'],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        //$verified = $this->verifyWebhook();
        //$verified = $this->verifyWebhook();
        //  var_export($verified, true);
        //  die;
        //throw new NotFoundHttpException('Unsupported action request.')



        $headers = Yii::$app->request->headers;

        $domain = $headers->get('x-shopify-shop-domain');
        $type = $headers->get('x-shopify-topic');

        /** @var CustomerMeta $customerMeta */
        $customerMeta = Yii::$app->customerSettings->getObjectByValue('shopify_store_url', $domain);

        $shopifyData = Yii::$app->request->bodyParams;
        // @todo not the best way to do this and we need to verify that this is the correct shopify user
        $this->apiConsumer = ApiConsumer::find()->where(['customer_id' => $customerMeta->customer_id])->one();
        if (!$this->apiConsumer) {
            $this->apiConsumer = new ApiConsumer(['customer_id' => $customerMeta->customer_id]);
        }
        Yii::debug($this->apiConsumer);
        $orderForm = new ShopifyMapper();
        $orderForm->setScenario('create');
        $orderForm->scenario = ShopifyMapper::SCENARIO_DEFAULT;
        $orderForm->setAttributes($orderForm->parse($shopifyData));
        return $this->orderCreate($orderForm);

    }


    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $verified = $this->verifyWebhook();
        var_export($verified, true);
        die;
        //throw new NotFoundHttpException('Unsupported action request.');
    }

    protected function verifyWebhook()
    {
        $shopifySharedSecret = CustomerMeta::find()->where([
            'customer_id' => \Yii::$app->user->identity->customer->id,
            'key' => 'shopify_shared_secret',
        ])->one();

        return hash_equals(\Yii::$app->request->headers->get('HTTP_X_SHOPIFY_HMAC_SHA256'),
            base64_encode(hash_hmac('sha256', \Yii::$app->getRequest()
                ->getRawBody(), $shopifySharedSecret->value, true)));
    }

    public function actionShopifyverify()
    {
        $verified = $this->verifyWebhook();
        error_log('Webhook verified: ' . var_export($verified, true)); //check error.log to see the result
    }


    public function behaviors()
    {
        return Controller::behaviors();
    }
}