<?php

namespace console\jobs\subscription\stripe;

use yii\web\NotFoundHttpException;

/**
 * Class StripeCustomerSubscriptionTrialWillEndJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.trial_will_end
 * @see https://stripe.com/docs/api/subscriptions/object
 */
class StripeCustomerSubscriptionTrialWillEndJob extends BaseStripeJob
{
    /**
     * Since we update our internal subscription via `customer.subscription.updated`, we skip this webhook.
     * Use it in case you need to do something 3 days before the trial ends.
     * @see https://stripe.com/docs/api/events/types#event_types-customer.subscription.trial_will_end
     * @throws NotFoundHttpException
     */
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            try {
                // ...
                $this->subscriptionWebhook->setSuccess();
            } catch (\Exception $e) {
                $error = serialize($e);
                $this->subscriptionWebhook->setFailed(true, $error);
            }
        }
    }
}
