<?php

namespace frontend\controllers;

use common\models\Customer;
use dektrium\user\controllers\AdminController;
use frontend\models\PaymentMethod;
use Yii;
use yii\helpers\ArrayHelper;

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
     * @var string[] $excludedRoutes List of excluded routes from being redirected to billing create page
     */
    public $excludedRoutes = [
        'billing/create',
        'user/register',
    ];

    /** @var array Customers that belong to the current user */
    public $customers = [];

    /** @var array Customer IDs as values that belong to the current user */
    public $customer_ids = [];


    public function init()
    {
        parent::init();
        // Only perform this check if a user is logged in
        if (!Yii::$app->user->isGuest) {
            if (!Yii::$app->session->has(AdminController::ORIGINAL_USER_SESSION_KEY) &&
                !Yii::$app->user->identity->isAdmin &&
                Yii::$app->user->identity->isDirectCustomer() &&
                !Yii::$app->user->identity->hasPaymentMethod() &&
                !in_array($this->module->requestedRoute, $this->excludedRoutes)) {

                Yii::$app->getSession()->setFlash('error', 'Please Set Up Payment Method and Add A Card To Your Profile.');

                //Cant return because init function does not return.
                $this->redirect(['/billing/create']);

                //Need to stop the app from continuing to display the Flash Message.
                Yii::$app->end();
            }

            /**
             * Set the customer ids for all controllers
             */
            $query = Customer::find();
            $query = (!Yii::$app->user->identity->isAdmin) ? $query->where(['in', 'id', Yii::$app->user->identity->customerIds]): $query;
            $this->customers = $query->all();
            $this->customer_ids = ArrayHelper::map($this->customers, 'name', 'id');
        }
    }
}
