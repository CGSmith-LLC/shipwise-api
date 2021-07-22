<?php

namespace api\modules\v1\controllers;

use api\modules\v1\components\ControllerEx;
use api\modules\v1\models\core\ApiConsumerEx;
use api\modules\v1\models\forms\OrderForm;
use api\modules\v1\models\mappers\ShopifyMapper;
use common\models\ApiConsumer;
use common\models\CustomerMeta;
use common\models\Order;
use common\models\Status;
use console\jobs\ShopifyJob;
use shopify\controllers\BaseController;
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
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $headers = Yii::$app->request->headers;

        // Is Shopify?

        // Is WooCommerce
        Yii::debug($headers->get('user-agent')); // should be WooCommerce

        $key = 'O=x)W6j6jBzITAwG+{PxV)d>M;;K3M:u2sfkpo6/v;?><P!+}F';

        Yii::debug(base64_encode(hash_hmac('sha256', Yii::$app->request->getRawBody(), $key, true)));
        if (strpos($headers->get('user-agent'), 'WooCommerce') !== false) {
            if (strpos($headers->get('X-WC-Webhook-Source'), 'https://bluenestbeef.com') !== false) {
                return $this->success(200, 'Received');
            }else {
                // Webhook was called
                return $this->errorMessage(200, 'Error saving webhook data');
            }
            // X-WC-Webhook-Source = originating Customer URL
            // X-WC-Webhook-Topic = order.created - create order

        }

        $id = Yii::$app->queue->push(new ShopifyJob([
            'headers' => Yii::$app->request->headers,
            'body' => Yii::$app->request->bodyParams])
        );

        return $this->success($id, 201);
//M9M4-U43C-9ZET-6VBZ
        $domain = $headers->get('x-shopify-shop-domain');
        $type = $headers->get('x-shopify-topic');

        /** @var CustomerMeta $customerMeta */
        $customerMeta = Yii::$app->customerSettings->getObjectByValue('shopify_store_url', $domain);
        $shopifyData = Yii::$app->request->bodyParams;

        $this->apiConsumer = ApiConsumer::find()->where(['customer_id' => $customerMeta->customer_id])->one();
        if (!$this->apiConsumer) {
            $this->apiConsumer = new ApiConsumer(['customer_id' => $customerMeta->customer_id]);
        }
        if ($type == 'orders/create') {
            // @todo not the best way to do this and we need to verify that this is the correct shopify user
            if (!empty(Order::find()->where(['uuid' => $shopifyData['id']])->all())) {
                return $this->errorMessage(400, "An order with this id already exists");
            }
            $orderForm = new ShopifyMapper();
            $orderForm->setScenario(ShopifyMapper::SCENARIO_DEFAULT);
            $orderForm->setAttributes($orderForm->parse($shopifyData));
            return $this->orderCreate($orderForm);
        } elseif ($type == 'orders/delete') {
            $toDelete = Order::find()->where(['uuid' => $shopifyData['id'], 'status_id' => Status::OPEN])->one();
            Yii::debug($toDelete);
            return $toDelete->delete();
        } elseif ($type == 'orders/cancelled') {
            $toCancel = Order::find()->where(['uuid' => $shopifyData['id'], 'status_id' => Status::OPEN])->one();
            $toCancel->status_id = Status::CANCELLED;
            return $toCancel->save();
        } elseif ($type == 'orders/updated') {
            $orderForm = new ShopifyMapper();
            $orderForm->setScenario(ShopifyMapper::SCENARIO_UPDATE);
            $orderForm->setAttributes($orderForm->parse($shopifyData));
            $toUpdate = Order::find()->where(['uuid' => $shopifyData['id'], 'status_id' => Status::OPEN])->one();
            $id = $toUpdate->id;
            return $this->orderUpdate($orderForm, $id);
        }
    }



    public function behaviors()
    {
        return Controller::behaviors();
    }
}