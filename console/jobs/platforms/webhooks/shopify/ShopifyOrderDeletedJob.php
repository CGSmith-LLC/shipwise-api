<?php

namespace console\jobs\platforms\webhooks\shopify;

use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use yii\helpers\Json;

/**
 * Class ShopifyOrderDeletedJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-orders-delete
 */
class ShopifyOrderDeletedJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->delete()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    /**
     * @throws \yii\db\StaleObjectException
     * @throws \Throwable
     */
    protected function delete(): bool
    {
        $payload = Json::decode($this->ecommerceWebhook->payload);

        if (isset($payload['id'])) {
            $internalOrder = $this->getOrderByExternalId((int)$payload['id']);

            if ($internalOrder) {
                $internalOrder->delete();
                return true;
            }
        }

        return false;
    }
}
