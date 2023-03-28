<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyCustomerRedactJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/apps/webhooks/configuration/mandatory-webhooks#customers-redact
 */
class ShopifyCustomerRedactJob extends BaseWebhookProcessingJob
{
    /**
     * Since we don't save information (data) about Shopify customers, we skip this mandatory webhook
     */
    public function execute($queue): void
    {
        parent::execute($queue);
        $this->ecommerceWebhook->setSuccess();
    }
}
