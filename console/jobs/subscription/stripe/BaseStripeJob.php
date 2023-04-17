<?php

namespace console\jobs\subscription\stripe;

use common\services\subscription\SubscriptionService;
use frontend\models\Customer;
use yii\base\BaseObject;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\queue\RetryableJobInterface;
use common\models\SubscriptionWebhook;

/**
 * Class BaseStripeJob
 * @package console\jobs\subscription\stripe
 */
class BaseStripeJob extends BaseObject implements RetryableJobInterface
{
    public array $payload;
    public int $subscriptionWebhookId;

    protected bool $isExecutable = false;
    protected ?SubscriptionWebhook $subscriptionWebhook = null;
    protected ?Customer $customer = null;
    protected ?SubscriptionService $subscriptionService = null;

    /**
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        $this->setSubscriptionWebhook();
        $this->isExecutable = $this->subscriptionWebhook->isReceived();

        if ($this->isExecutable) {
            $this->subscriptionWebhook->setProcessing();

            try {
                $this->setCustomer();
                $this->setSubscriptionService();
            }  catch (\Exception $e) {
                $this->subscriptionWebhook->setFailed(true, Json::encode(['error' => $e->getMessage()]));
            }
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setSubscriptionWebhook(): void
    {
        $subscriptionWebhook = SubscriptionWebhook::findOne($this->subscriptionWebhookId);

        if (!$subscriptionWebhook) {
            throw new NotFoundHttpException('Subscription webhook not found.');
        }

        $this->subscriptionWebhook = $subscriptionWebhook;
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setCustomer(): void
    {
        // `client_reference_id` is used in pricing table
        // used until the field `stripe_customer_id` is not set
        if (isset($this->payload['data']['object']['client_reference_id'])) {
            $condition = ['id' => (int)$this->payload['data']['object']['client_reference_id']];
        } elseif (isset($this->payload['data']['object']['customer'])) {
            $condition = ['stripe_customer_id' => $this->payload['data']['object']['customer']];
        } else {
            $condition = [];
        }

        /**
         * @var $customer Customer
         */
        $customer = Customer::find()
            ->where($condition)
            ->one();

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $this->customer = $customer;
    }

    protected function setSubscriptionService(): void
    {
        $this->subscriptionService = new SubscriptionService($this->customer);
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
