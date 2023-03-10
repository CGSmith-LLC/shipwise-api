<?php

namespace console\jobs\platforms\shopify;

use Yii;
use common\models\EcommerceIntegration;
use common\services\platforms\ShopifyService;
use yii\base\{BaseObject, InvalidConfigException};
use yii\helpers\{Json, Url};
use yii\queue\RetryableJobInterface;
use yii\web\NotFoundHttpException;

/**
 * Class RegisterShopifyWebhookListenersJob
 * @package console\jobs\platforms\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics
 */
class RegisterShopifyWebhookListenersJob extends BaseObject implements RetryableJobInterface
{
    public int $ecommerceIntegrationId;

    protected ?EcommerceIntegration $ecommerceIntegration = null;
    protected ?ShopifyService $shopifyService = null;
    protected ?string $domain = null;

    /**
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function execute($queue): void
    {
        // Setters:
        $this->setEcommerceIntegration();
        $this->setShopifyService();
        $this->setDomain();

        // Register listeners:
        foreach (ShopifyService::$webhookListeners as $listener) {
            $this->sendRequest($listener);
        }

        // Update integration:
        $this->updateIntegrationMetaData();
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setEcommerceIntegration(): void
    {
        $ecommerceIntegration = EcommerceIntegration::findOne($this->ecommerceIntegrationId);

        if (!$ecommerceIntegration) {
            throw new NotFoundHttpException('E-commerce integration not found.');
        }

        $this->ecommerceIntegration = $ecommerceIntegration;
    }

    /**
     * @throws InvalidConfigException
     */
    protected function setShopifyService(): void
    {
        $this->shopifyService = new ShopifyService($this->ecommerceIntegration->array_meta_data['shop_url'], $this->ecommerceIntegration);
    }

    protected function setDomain(): void
    {
        $this->domain = trim(Url::to(['/'], true), '/');

        if (Yii::$app->params['shopify']['override_redirect_domain'] != false) {
            $this->domain = Yii::$app->params['shopify']['override_redirect_domain'];
        }
    }

    protected function updateIntegrationMetaData(): void
    {
        $this->ecommerceIntegration->array_meta_data['connected_webhook_listeners'] = ShopifyService::$webhookListeners;
        $this->ecommerceIntegration->meta = Json::encode($this->ecommerceIntegration->array_meta_data, JSON_PRETTY_PRINT);
        $this->ecommerceIntegration->save();
    }

    protected function sendRequest(string $event): void
    {
        $res = $this->shopifyService->createWebhook([
            'topic' => $event,
            'address' => $this->domain . ShopifyService::$webhooksUrl . '?event=' . $event,
            'format' => 'json',
        ]);

//        echo '<pre>' . $event . ': ';
//        print_r($res);
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }

    public function getTtr(): int
    {
        return 5 * 60;
    }
}
