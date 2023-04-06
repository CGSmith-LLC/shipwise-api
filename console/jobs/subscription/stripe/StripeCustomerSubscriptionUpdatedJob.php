<?php

namespace console\jobs\subscription\stripe;

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
                $error = serialize($e);
                $this->subscriptionWebhook->setFailed(true, $error);
            }
        }
    }

    protected function updateSubscription(): void
    {

    }
}
