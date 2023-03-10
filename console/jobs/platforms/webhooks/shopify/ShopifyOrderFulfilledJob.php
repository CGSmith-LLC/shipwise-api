<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderFulfilledJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderFulfilledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " fulfilled ";
    }
}
