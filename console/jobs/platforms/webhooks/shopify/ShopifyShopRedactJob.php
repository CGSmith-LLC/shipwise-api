<?php

namespace console\jobs\platforms\webhooks\shopify;

use common\models\EcommerceIntegration;
use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use yii\helpers\Json;

/**
 * Class ShopifyShopRedactJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/apps/webhooks/configuration/mandatory-webhooks#shop-redact
 */
class ShopifyShopRedactJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->deleteEcommerceIntegration()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    protected function deleteEcommerceIntegration(): bool
    {
        $payload = Json::decode($this->ecommerceWebhook->payload);

        if (isset($payload['shop_domain'])) {
            /**
             * @var $ecommerceIntegration EcommerceIntegration
             */
            $ecommerceIntegration = EcommerceIntegration::find()
                ->byMetaKey('shop_url', trim($payload['shop_domain']))
                ->one();

            if ($ecommerceIntegration) {
                $ecommerceIntegration->disconnect();

                return true;
            }
        }

        return false;
    }
}
