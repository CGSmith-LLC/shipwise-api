<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderPaidJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyOrderPaidJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " paid ";
        $this->ecommerceWebhook->setSuccess();
    }
}
