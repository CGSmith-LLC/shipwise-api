<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyAppUninstalledJob
 * @package console\jobs\platforms\webhooks\shopify
 */
class ShopifyAppUninstalledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " app uninstalled ";
        $this->ecommerceWebhook->setSuccess();
    }
}
