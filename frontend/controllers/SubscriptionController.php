<?php

namespace frontend\controllers;

use yii\web\{NotFoundHttpException, Response};
use common\models\Subscription;
use Stripe\Exception\ApiErrorException;
use frontend\models\Customer;
use common\services\subscription\SubscriptionService;
use yii\filters\AccessControl;

class SubscriptionController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionIndex(): string
    {
        $customer = $this->getCustomer();
        $subscriptionService = new SubscriptionService($customer);

        return $this->render('index', [
            'subscriptionService' => $subscriptionService,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     * @throws ApiErrorException
     */
    public function actionInvoice(int $id): Response
    {
        $customer = $this->getCustomer();
        $subscription = Subscription::findOne($id);

        if (!$subscription || $subscription->customer_id != $customer->id) {
            throw new NotFoundHttpException('Invoice not found.');
        }

        $subscriptionService = new SubscriptionService($customer);
        $stripeInvoice = $subscriptionService->getInvoiceObjectById($subscription->array_meta_data['latest_invoice']);

        if (!$stripeInvoice) {
            throw new NotFoundHttpException('Invoice not found.');
        }

        return $this->redirect($stripeInvoice['hosted_invoice_url']);
    }

    /**
     * TODO: implement the logic
     * @throws NotFoundHttpException
     */
    protected function getCustomer(): ?Customer
    {
        $customer = Customer::findOne(1);

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found.');
        }

        return $customer;
    }
}
