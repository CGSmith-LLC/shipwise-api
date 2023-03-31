<?php

namespace frontend\controllers;

use frontend\models\Customer;
use Yii;
use common\services\subscription\SubscriptionService;

class SubscriptionController extends Controller
{

    /**
     * Lists all AliasParent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $customer = Customer::findOne(1);
        $subscriptionService = new SubscriptionService($customer);

        return $this->render('index', [

        ]);
    }
}
