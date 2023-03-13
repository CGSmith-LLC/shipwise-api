<?php

namespace console\jobs\platforms\webhooks\shopify;

use common\models\Status;
use console\jobs\platforms\webhooks\BaseWebhookProcessingJob;
use yii\helpers\Json;

/**
 * Class ShopifyOrderCancelledJob
 * @package console\jobs\platforms\webhooks\shopify
 * @see https://shopify.dev/docs/api/admin-rest/2023-01/resources/webhook#event-topics-orders-cancelled
 */
class ShopifyOrderCancelledJob extends BaseWebhookProcessingJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->cancel()) {
            $this->ecommerceWebhook->setSuccess();
        } else {
            $this->ecommerceWebhook->setFailed();
        }
    }

    protected function cancel(): bool
    {
        $payload = Json::decode($this->ecommerceWebhook->payload);

        if (isset($payload['id'])) {
            $externalOrderId = (int)$payload['id'];
            $internalOrder = $this->getOrderByExternalId($externalOrderId);

            if ($internalOrder) {
                $internalOrder->status_id = Status::CANCELLED;
                $internalOrder->save();
                return true;
            }
        }

        return false;
    }
}
