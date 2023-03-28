<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyOrderRefundJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-refunds-create
 */
class ShopifyOrderRefundJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);
        echo " refund ";

        if ($this->refund()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    protected function refund(): bool
    {
        return true;
    }
}
