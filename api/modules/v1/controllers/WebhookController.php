<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\core\ApiConsumerEx;
use common\models\CustomerMeta;
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
            'index'        => ['GET'],
            'shopify'      => ['POST'],
        ];
    }
    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $verified = $this->verifyWebhook();
        var_export($verified, true);die;
        //throw new NotFoundHttpException('Unsupported action request.');
    }
    /**
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        $verified = $this->verifyWebhook();
        var_export($verified, true);die;
        //throw new NotFoundHttpException('Unsupported action request.');
    }

    protected function verifyWebhook()
    {
        $shopifySharedSecret = CustomerMeta::find()
        ->where([
            'customer_id' => \Yii::$app->user->identity->customer->id,
            'key' => 'shopify_shared_secret'
        ])
        ->one();
        return hash_equals(\Yii::$app->request->headers->get('HTTP_X_SHOPIFY_HMAC_SHA256'),
            base64_encode(hash_hmac('sha256', \Yii::$app->getRequest()->getRawBody(), $shopifySharedSecret->value, true)));
    }

    public function actionShopifyverify()
    {
        $verified = $this->verifyWebhook();
        error_log('Webhook verified: ' . var_export($verified, true)); //check error.log to see the result
    }

}