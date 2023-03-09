<?php

namespace frontend\controllers;

use common\models\EcommerceIntegration;
use common\services\platforms\ShopifyService;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use Da\User\Filter\AccessRuleFilter;
use common\models\EcommercePlatform;
use common\models\search\EcommercePlatformSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionShopify(): array
    {
        $event = Yii::$app->request->get('event');

        if (!in_array($event, ShopifyService::$webhookListeners)) {
            throw new NotFoundHttpException('Event not found.');
        }

        return [
            'result' => 'success',
        ];
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
