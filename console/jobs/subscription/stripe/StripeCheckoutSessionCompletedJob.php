<?php

namespace console\jobs\subscription\stripe;

use Stripe\Exception\ApiErrorException;
use frontend\models\Customer;
use common\services\subscription\SubscriptionService;
use Stripe\{Plan, Price, Subscription, SubscriptionItem};
use common\models\SubscriptionHistory;
use yii\web\{ServerErrorHttpException, NotFoundHttpException};

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
    protected ?Customer $customer = null;
    protected ?SubscriptionService $subscriptionService = null;

    public function execute($queue): void
    {
        parent::execute($queue);

        if ($this->isExecutable()) {
            if ($this->isPaid()) {
                try {
                    $this->setCustomer();
                    $this->setSubscriptionService();

                    $this->updateStripeCustomerId();
                    $this->addSubscription();

                    $this->subscriptionWebhook->setSuccess();
                } catch (\Exception $e) {
                    $error = serialize($e);
                    $this->subscriptionWebhook->setFailed(true, $error);
                }
            } else {
                $this->subscriptionWebhook->setFailed(true, 'payment_status != paid');
            }
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function setCustomer(): void
    {
        $customer = Customer::findOne((int)$this->payload['data']['object']['client_reference_id']);

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $this->customer = $customer;
    }

    protected function setSubscriptionService(): void
    {
        $this->subscriptionService = new SubscriptionService($this->customer);
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
     * @throws ApiErrorException
     */
    protected function addSubscription(): void
    {
        /**
         * @var $stripeSubscriptionObject Subscription
         * @var $subscription SubscriptionItem
         * @var $plan Plan
         * @var $price Price
         */
        $stripeSubscriptionObject = $this->subscriptionService->getSubscriptionObjectById($this->payload['data']['object']['subscription']);
        $subscription = $stripeSubscriptionObject->items->data[0];
        $plan = $subscription->plan;
        $product = $this->subscriptionService->getProductObjectById($plan->product);
        $price = $subscription->price;

        $activeSubscription = $this->subscriptionService->addSubscription([
            'customer_id' => $this->customer->id,
            'payment_method' => SubscriptionService::PAYMENT_METHOD_STRIPE,
            'payment_method_subscription_id' => $stripeSubscriptionObject->id,
            'is_active' => SubscriptionHistory::IS_TRUE,
            'is_trial' => ($stripeSubscriptionObject->trial_start) ? SubscriptionHistory::IS_TRUE : SubscriptionHistory::IS_FALSE,
            'status' => $stripeSubscriptionObject->status,
            'paid_amount' => $this->payload['data']['object']['amount_total'],
            'paid_currency' => $stripeSubscriptionObject->currency,
            'plan_name' => $product->name,
            'plan_interval' => $plan->interval,
            'plan_period_start' => date("Y-m-d H:i:s", $stripeSubscriptionObject->current_period_start),
            'plan_period_end' => date("Y-m-d H:i:s", $stripeSubscriptionObject->current_period_end),
            'meta' => $stripeSubscriptionObject->toJSON()
        ]);

        if (!$activeSubscription) {
            throw new ServerErrorHttpException('Subscription history not saved.');
        }

        $this->subscriptionService->makeSubscriptionsInactive($activeSubscription);
    }
}
