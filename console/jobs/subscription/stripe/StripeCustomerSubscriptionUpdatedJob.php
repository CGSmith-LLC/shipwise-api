<?php

namespace console\jobs\subscription\stripe;

use common\models\Subscription;
use yii\helpers\Json;
use yii\web\ServerErrorHttpException;

/**
 * Class StripeCustomerSubscriptionUpdatedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.updated
 * @see https://stripe.com/docs/api/subscriptions/object
 */
class StripeCustomerSubscriptionUpdatedJob extends BaseStripeJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            try {
                $this->updateSubscription();
                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $this->subscriptionWebhook->setFailed(true, Json::encode(['error' => $e->getMessage()]));
            }
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function updateSubscription(): void
    {
        $subscription = $this->subscriptionService
            ->getSubscriptionByPaymentMethodSubscriptionId($this->payload['data']['object']['id']);

        if (!$subscription) {
            throw new ServerErrorHttpException('Subscription not found.');
        }

        $previousAttributes = $this->payload['data']['previous_attributes'];

        // Subscription period:

        if (isset($previousAttributes['current_period_start'])) {
            $subscription->current_period_start = date("Y-m-d H:i:s", $this->payload['data']['object']['current_period_start']);
        }

        if (isset($previousAttributes['current_period_end'])) {
            $subscription->current_period_end = date("Y-m-d H:i:s", $this->payload['data']['object']['current_period_end']);
        }

        // Subscription status:

        if (isset($previousAttributes['status'])) {
            $status = $this->payload['data']['object']['status'];
            $subscription->status = $status;
            $subscription->is_trial = ($status == Subscription::STATUS_TRIAL);
            $subscription->is_active = (in_array($status, [Subscription::STATUS_TRIAL, Subscription::STATUS_ACTIVE]));
        }

        // Plan changed:

        if (isset($previousAttributes['items']['data'][0]['plan'])) {
            $plan = $this->payload['data']['object']['items']['data'][0]['plan'];
            $subscription->plan_interval = $plan['interval'];
            $subscription->plan_name = $plan['name'];
        }

        $subscription->meta = Json::encode($this->payload['data']['object'], JSON_PRETTY_PRINT);

        if (!$subscription->save()) {
            throw new ServerErrorHttpException('Subscription not updated.');
        }
    }
}
