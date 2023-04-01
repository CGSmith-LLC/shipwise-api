<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Customer;
use common\services\subscription\SubscriptionService;

class SubscriptionController extends Controller
{
    public function actionIndex(): string
    {
        $customer = Customer::findOne(1);
        $subscriptionService = new SubscriptionService($customer);

        return $this->render('index', [
            'subscriptionService' => $subscriptionService,
        ]);
    }
}
