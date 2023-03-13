<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderDeletedJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderDeletedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " deleted ";
        $this->ecommerceWebhook->setSuccess();
    }
}
