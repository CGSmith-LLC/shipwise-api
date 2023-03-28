<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderPartiallyFulfilledJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderPartiallyFulfilledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " part. fulfilled ";
        $this->ecommerceWebhook->setSuccess();
    }
}
