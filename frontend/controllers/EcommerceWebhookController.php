<?php

namespace frontend\controllers;

use Yii;
use yii\web\{BadRequestHttpException, Response, Controller, NotFoundHttpException, ServerErrorHttpException};
use common\models\{EcommercePlatform, EcommerceWebhook, EcommerceIntegration};
use common\services\platforms\ShopifyService;

/**
 * Class EcommerceWebhookController
 * @package frontend\controllers
 */
class EcommerceWebhookController extends Controller
{
    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    /**
     * Receives and saves webhooks from the Shopify e-commerce platform.
     * @return array
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionShopify(): array
    {
        // file_put_contents('shopify.txt', "\r\n\r\n\r\n" . Yii::$app->request->rawBody, FILE_APPEND);


        $ecommercePlatform = $this->getEcommercePlatformByName(EcommercePlatform::SHOPIFY_PLATFORM_NAME);
        $event = Yii::$app->request->get('event');

        if (!in_array($event, ShopifyService::$webhookListeners) &&
            !in_array($event, ShopifyService::$mandatoryWebhookListeners)) {
            throw new NotFoundHttpException('Event not found.');
        }

        $ecommerceWebhook = $this->getNewEcommerceWebhookObject($ecommercePlatform->id);
        $ecommerceWebhook->event = $event;
        $ecommerceWebhook->payload = Yii::$app->request->rawBody;

        if (!$ecommerceWebhook->save()) {
            throw new ServerErrorHttpException('Event not saved.');
        }

        return [
            'status' => 200,
            'result' => 'success',
        ];
    }

    /**
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function getEcommercePlatformByName(string $name): EcommercePlatform
    {
        /**
         * @var EcommercePlatform $model
         */
        $model = EcommercePlatform::find()
            ->where(['name' => $name])
            ->one();

        if (!$model) {
            throw new NotFoundHttpException('Ecommerce platform does not exist.');
        }

        if (!$model->isActive()) {
            throw new ServerErrorHttpException('Ecommerce platform is not active.');
        }

        return $model;
    }

    protected function getNewEcommerceWebhookObject(int $ecommercePlatformId): EcommerceWebhook
    {
        $ecommerceWebhook = new EcommerceWebhook();
        $ecommerceWebhook->platform_id = $ecommercePlatformId;
        $ecommerceWebhook->status = EcommerceWebhook::STATUS_RECEIVED;

        return $ecommerceWebhook;
    }

    public function actionTest()
    {
        $ecommerceIntegrations = EcommerceIntegration::find()
            ->active()
            ->orderById()
            ->all();

        foreach ($ecommerceIntegrations as $ecommerceIntegration) {
            $accessToken = $ecommerceIntegration->array_meta_data['access_token'];

            if ($accessToken) {
                $shopifyService = new ShopifyService($ecommerceIntegration->array_meta_data['shop_url'], $ecommerceIntegration);
                return $shopifyService->getWebhooksList();
            }
        }
    }
}
