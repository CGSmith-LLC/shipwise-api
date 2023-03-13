<?php

namespace console\jobs\platforms\webhooks\shopify;

use common\models\EcommerceIntegration;
use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use yii\helpers\Json;

/**
 * Class ShopifyAppUninstalledJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-app-uninstalled
 */
class ShopifyAppUninstalledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->uninstall()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    protected function uninstall(): bool
    {
        $payload = Json::decode($this->ecommerceWebhook->payload);
        $shopUrl = null;

        if (isset($payload['domain'])) {
            $shopUrl = trim($payload['domain']);
        }

        if ($shopUrl) {
            /**
             * @var $ecommerceIntegration EcommerceIntegration
             */
            $ecommerceIntegration = EcommerceIntegration::find()
                ->active()
                ->byMetaKey('shop_url', $shopUrl)
                ->one();

            if ($ecommerceIntegration && !$ecommerceIntegration->isUninstalled()) {
                $ecommerceIntegration->uninstall(true);

                return true;
            }
        }

        return false;
    }
}
