<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderCreatedJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderCreatedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " created ";
    }
}
