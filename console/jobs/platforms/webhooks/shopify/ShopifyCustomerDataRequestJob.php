<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyCustomerDataRequestJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyCustomerDataRequestJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " customer data request ";
        $this->ecommerceWebhook->setSuccess();
    }
}
