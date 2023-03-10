<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyCustomerRedactJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyCustomerRedactJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " customer redact ";
    }
}
