<?php

namespace console\jobs\subscription\stripe;

use yii\helpers\Json;

/**
 * Class StripeCustomerSubscriptionResumedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.resumed
 */
class StripeCustomerSubscriptionResumedJob extends BaseStripeJob
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

    protected function updateSubscription(): void
    {

    }
}
