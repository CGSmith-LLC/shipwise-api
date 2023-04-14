<?php

namespace console\jobs\subscription\stripe;

use yii\helpers\Json;
use yii\web\ServerErrorHttpException;
use common\services\subscription\SubscriptionService;
use common\models\Subscription;

/**
 * Class StripeCustomerSubscriptionCreatedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.created
 * @see https://stripe.com/docs/api/subscriptions/object
 */
class StripeCustomerSubscriptionCreatedJob extends BaseStripeJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            try {
                $this->addSubscription();

                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $this->subscriptionWebhook->setFailed(true, Json::encode(['error' => $e->getMessage()]));
            }
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function addSubscription(): void
    {
        $subscriptionObject = $this->payload['data']['object'];

        $params = [
            'customer_id' => $this->customer->id,
            'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
            'payment_method_subscription_id' => $subscriptionObject['id'],
            'is_active' => in_array($subscriptionObject['status'], [Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIAL]),
            'is_trial' => (bool) $subscriptionObject['trial_start'],
            'status' => $subscriptionObject['status'],
            'plan_name' => $subscriptionObject['plan']['name'],
            'plan_interval' => $subscriptionObject['plan']['interval'],
            'current_period_start' => date("Y-m-d H:i:s", $subscriptionObject['current_period_start']),
            'current_period_end' => date("Y-m-d H:i:s", $subscriptionObject['current_period_end']),
            'meta' => Json::encode($subscriptionObject, JSON_PRETTY_PRINT),
        ];

        $activeSubscription = $this->subscriptionService->addSubscription($params);

        if (!$activeSubscription) {
            throw new ServerErrorHttpException('Subscription not saved.');
        }

        $this->subscriptionService->makeSubscriptionsInactive($activeSubscription);
    }
}
