<?php

namespace console\jobs\subscription\stripe;

use common\services\subscription\SubscriptionService;
use Stripe\{Plan, SubscriptionItem};
use Stripe\Subscription as StripeSubscription;
use yii\helpers\Json;
use yii\web\ServerErrorHttpException;

/**
 * Class StripeCheckoutSessionCompletedJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/api/events/types#event_types-checkout.session.completed
 * @see https://stripe.com/docs/api/checkout/sessions/object
 * @see https://stripe.com/docs/api/subscriptions/object
 * @see https://stripe.com/docs/payments/checkout/fulfill-orders#fulfill
 * @see https://stripe.com/docs/no-code/pricing-table#handle-fulfillment-with-the-stripe-api
 */
class StripeCheckoutSessionCompletedJob extends BaseStripeJob
{
    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable) {
            if ($this->isPaid()) {
                try {
                    $this->updateStripeCustomerId();
                    $this->updateSubscription();

                    $this->subscriptionWebhook->setSuccess();
                } catch (\Exception $e) {
                    $this->subscriptionWebhook->setFailed(true, Json::encode(['error' => $e->getMessage()]));
                }
            } else {
                $this->subscriptionWebhook->setFailed(true, 'payment_status != paid');
            }
        }
    }

    protected function isPaid(): bool
    {
        return $this->payload['data']['object']['payment_status'] == 'paid';
    }

    protected function updateStripeCustomerId(): void
    {
        $this->customer->stripe_customer_id = $this->payload['data']['object']['customer'];
        $this->customer->save();
    }

    /**
     * @throws ServerErrorHttpException
     */
    protected function updateSubscription(): void
    {
        /**
         * @var $stripeSubscriptionObject StripeSubscription
         * @var $subscription SubscriptionItem
         * @var $plan Plan
         */
        $stripeSubscriptionObject = $this->subscriptionService->getSubscriptionObjectById($this->payload['data']['object']['subscription']);
        $subscription = $stripeSubscriptionObject->items->data[0];
        $plan = $subscription->plan;
        $product = $this->subscriptionService->getProductObjectById($plan->product);

        $params = [
            'customer_id' => $this->customer->id,
            'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
            'payment_method_subscription_id' => $stripeSubscriptionObject->id,
            'is_active' => 1,
            'is_trial' => (bool) $stripeSubscriptionObject->trial_start,
            'status' => $stripeSubscriptionObject->status,
            'plan_name' => $product->name,
            'plan_interval' => $plan->interval,
            'current_period_start' => date("Y-m-d H:i:s", $stripeSubscriptionObject->current_period_start),
            'current_period_end' => date("Y-m-d H:i:s", $stripeSubscriptionObject->current_period_end),
            'meta' => $stripeSubscriptionObject->toJSON()
        ];

        $activeSubscription = $this->subscriptionService->getSubscriptionByPaymentMethodSubscriptionId($stripeSubscriptionObject->id);

        if (!$activeSubscription) {
            $activeSubscription = $this->subscriptionService->addSubscription($params);
        } else {
            $activeSubscription = $this->subscriptionService->updateSubscription($stripeSubscriptionObject->id, $params);
        }

        if (!$activeSubscription) {
            throw new ServerErrorHttpException('Subscription not saved.');
        }

        $this->subscriptionService->makeSubscriptionsInactive($activeSubscription);
    }
}
