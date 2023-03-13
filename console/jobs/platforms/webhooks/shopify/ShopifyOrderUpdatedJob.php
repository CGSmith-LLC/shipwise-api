<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderUpdatedJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderUpdatedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " updated ";
        $this->ecommerceWebhook->setSuccess();
    }
}
