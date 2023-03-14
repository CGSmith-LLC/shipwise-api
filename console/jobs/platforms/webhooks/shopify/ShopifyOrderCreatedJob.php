<?php

namespace console\jobs\platforms\webhooks\shopify;

use Yii;
use yii\helpers\Json;
use common\models\EcommerceIntegration;
use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use console\jobs\platforms\shopify\ParseShopifyOrderJob;

/**
 * Class ShopifyOrderCreatedJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-orders-create
 */
class ShopifyOrderCreatedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        $payload = Json::decode($this->ecommerceWebhook->payload);
        $internalOrder = $this->getOrderByExternalId((int)$payload['id']);

        if ($internalOrder) {
            // If we already have an internal order with such UUID, we just do nothing:
            $this->ecommerceWebhook->setSuccess();
        } else {
            $ecommerceIntegration = $this->getEcommerceIntegration($payload);

            if ($ecommerceIntegration && $this->create($payload, $ecommerceIntegration)) {
                $this->ecommerceWebhook->setSuccess();
            } else {
                $this->ecommerceWebhook->setFailed();
            }
        }
    }

    /**
     * For parsing raw Shopify orders, we have a separate Job:
     */
    protected function create(array $payload, EcommerceIntegration $ecommerceIntegration): bool
    {
        Yii::$app->queue->push(
            new ParseShopifyOrderJob([
                'rawOrder' => $payload,
                'ecommerceIntegrationId' => $ecommerceIntegration->id
            ])
        );

        return true;
    }

    protected function getEcommerceIntegration(array $payload): EcommerceIntegration|bool
    {
        if (isset($payload['order_status_url']) && !empty($payload['order_status_url'])) {
            $domain = parse_url($payload['order_status_url'], PHP_URL_HOST);

            /**
             * @var $ecommerceIntegration EcommerceIntegration
             */
            $ecommerceIntegration = EcommerceIntegration::find()
                ->byMetaKey('shop_url', $domain)
                ->one();

            return ($ecommerceIntegration) ?: false;
        }

        return false;
    }
}
