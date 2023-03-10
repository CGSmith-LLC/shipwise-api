<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderCancelledJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderCancelledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " cancelled ";
    }
}
