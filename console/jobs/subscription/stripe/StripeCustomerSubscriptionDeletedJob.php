<?php

namespace console\jobs\subscription\stripe;

use yii\web\ServerErrorHttpException;

/**
 * Class StripeCustomerSubscriptionDeletedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.deleted
 * @see https://stripe.com/docs/billing/subscriptions/cancel
 */
class StripeCustomerSubscriptionDeletedJob extends BaseStripeJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            try {
                $this->deleteSubscription();
                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $error = serialize($e);
                $this->subscriptionWebhook->setFailed(true, $error);
            }
        }
    }

    protected function deleteSubscription(): void
    {
        $activeSubscription = $this->subscriptionService
            ->getSubscriptionByPaymentMethodSubscriptionId($this->payload['data']['object']['id']);

        if (!$activeSubscription) {
            throw new ServerErrorHttpException('Subscription not found.');
        }

        $activeSubscription->delete();
    }
}
