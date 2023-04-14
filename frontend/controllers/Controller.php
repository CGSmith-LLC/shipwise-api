<?php

namespace frontend\controllers;

use Yii;
use common\models\Customer;
use frontend\models\Customer as FrontendCustomer;
use common\services\subscription\SubscriptionService;
use yii\base\ExitException;
use yii\helpers\ArrayHelper;
use Da\User\Module as DaModule;

/**
 * Class Controller
 * Base controller responsible for checking if the user has any payment methods
 *
 * @package frontend\controllers
 * @property array $customers
 */
class Controller extends \yii\web\Controller
{
    /**
     * @var string[] $excludedRoutes List of excluded routes from being redirected to the subscription page.
     */
    protected array $excludedRoutes = [
        'user/login',
        'user/register',
        'user/resend',
        'user/logout',
        'subscription/index',
    ];

    /** @var array Customers that belong to the current user */
    protected array $customers = [];

    /** @var array Customer IDs as values that belong to the current user */
    protected array $customer_ids = [];

    /**
     * @throws ExitException
     */
    public function init(): void
    {
        parent::init();

        /**
         * @var $module DaModule
         */
        $module = Yii::$app->getModule('user');

        if (!Yii::$app->user->isGuest) {
            $this->subscription($module);
            $this->customerIds();
        }
    }

    /**
     * Check subscription.
     * @param DaModule $module
     * @throws ExitException
     */
    protected function subscription(DaModule $module): void
    {
        if (!Yii::$app->session->has($module->switchIdentitySessionKey) &&
            !Yii::$app->user->identity->isAdmin &&
            Yii::$app->user->identity->isDirectCustomer()) {
                /**
                 * TODO: implement the logic
                 */
                $customer = FrontendCustomer::findOne(1);
                $subscriptionService = new SubscriptionService($customer);

                if (!$subscriptionService->getActiveSubscription()
                    && !in_array($this->module->requestedRoute, $this->excludedRoutes)) {
                    Yii::$app->session->setFlash('error', 'Please subscribe to start using our service.');
                    $this->redirect(['/subscription/index']);
                    Yii::$app->end();
                }
        }
    }

    /**
     * Set the customer ids for all controllers.
     */
    protected function customerIds(): void
    {
        $query = Customer::find();
        $query = (!Yii::$app->user->identity->isAdmin) ? $query->where(['in', 'id', Yii::$app->user->identity->customerIds]): $query;
        $this->customers = $query->all();
        $this->customer_ids = ArrayHelper::map($this->customers, 'name', 'id');
    }
}
