<?php

namespace console\jobs\subscription\stripe;

use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use yii\web\{NotFoundHttpException, ServerErrorHttpException};
use common\models\Subscription;
use common\services\subscription\SubscriptionService;
use frontend\models\Customer;
use Stripe\Exception\ApiErrorException;

/**
 * Class StripeSubscriptionUpdateUsageJob
 * @package console\jobs\subscription\stripe
 *
 * @see https://stripe.com/docs/products-prices/pricing-models#reporting-usage
 */
class StripeSubscriptionUpdateUsageJob extends BaseObject implements RetryableJobInterface
{
    public int $subscriptionId;

    /**
     * @throws ServerErrorHttpException
     */
    public function execute($queue): void
    {
        try {
            $this->updateSubscriptionUsage();
        } catch (\Exception $e) {
            throw new ServerErrorHttpException($e->getMessage());
        }
    }

    /**
     * @throws ApiErrorException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    protected function updateSubscriptionUsage(): void
    {
        $subscription = Subscription::findOne($this->subscriptionId);

        if (!$subscription) {
            throw new NotFoundHttpException('Subscription not found.');
        }

        $customer = Customer::findOne($subscription->customer_id);

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        $subscriptionService = new SubscriptionService($customer);
        $subscriptionService->updateSubscriptionUsage($subscription);
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
