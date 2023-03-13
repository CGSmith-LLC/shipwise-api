<?php

namespace console\jobs\platforms\webhooks;

use yii\base\BaseObject;
use yii\helpers\Json;
use yii\queue\RetryableJobInterface;
use yii\web\NotFoundHttpException;
use common\models\{Order, EcommerceWebhook};

/**
 * Class BaseWebhookProcessingJob
 * @package console\jobs\platforms\webhooks
 */
abstract class BaseWebhookProcessingJob extends BaseObject implements RetryableJobInterface
{
    public int $ecommerceWebhookId;
    protected ?EcommerceWebhook $ecommerceWebhook = null;
    protected ?array $arrayPayload = null;

    /**
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        $this->setEcommerceWebhook();
        $this->ecommerceWebhook->setProcessing();

        if ($this->isPayloadJson()) {
            $this->arrayPayload = Json::decode($this->ecommerceWebhook->payload);
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setEcommerceWebhook(): void
    {
        $ecommerceWebhook = EcommerceWebhook::findOne($this->ecommerceWebhookId);

        if (!$ecommerceWebhook) {
            throw new NotFoundHttpException('E-commerce webhook not found.');
        }

        $this->ecommerceWebhook = $ecommerceWebhook;
    }

    /**
     * Tries to find our internal Order by provided external ID. External ID = uuid.
     * Use this method for webhook events like "order update/delete/cancel".
     * @param int $id
     * @return Order|null
     */
    protected function getOrderByExternalId(int $id): Order|null
    {
        return Order::find()->where(['uuid' => $id])->one();
    }

    protected function isPayloadJson(): bool
    {
        json_decode($this->ecommerceWebhook->payload);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }

    public function getTtr(): int
    {
        return 5 * 60;
    }
}
