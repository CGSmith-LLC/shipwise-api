<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyShopRedactJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyShopRedactJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " shop redact ";
        $this->ecommerceWebhook->setSuccess();
    }
}
