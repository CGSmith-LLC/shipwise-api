<?php

namespace console\jobs\subscription\stripe;

/**
 * Class StripeCustomerDeletedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.deleted
 */
class StripeCustomerDeletedJob extends BaseStripeJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            try {
                $this->deleteSubscriptions();
                $this->deleteStripeCustomerId();
                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $error = serialize($e);
                $this->subscriptionWebhook->setFailed(true, $error);
            }
        }
    }

    protected function deleteStripeCustomerId(): void
    {
        $this->customer->stripe_customer_id = null;
        $this->customer->save();
    }

    protected function deleteSubscriptions(): void
    {
        $subscriptions = $this->subscriptionService->getAllSubscriptions();

        foreach ($subscriptions as $subscription) {
            $subscription->delete();
        }
    }
}
