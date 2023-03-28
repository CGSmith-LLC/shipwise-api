<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;

/**
 * Class ShopifyCustomerDataRequestJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/apps/webhooks/configuration/mandatory-webhooks#customers-data_request
 */
class ShopifyCustomerDataRequestJob extends BaseWebhookProcessingJob
{
    /**
     * Quote:
     *
     * The webhook contains the resource IDs of the customer data that you need to provide to the store owner.
     * It's your responsibility to provide this data to the store owner directly.
     * In some cases, a customer record contains only the customer's email address.
     *
     * It means that it's enough just to mark the webhook as `success` since the `payload` field consists of the needed resource IDs.
     */
    public function execute($queue): void
    {
        parent::execute($queue);
        $this->ecommerceWebhook->setSuccess();
    }
}
