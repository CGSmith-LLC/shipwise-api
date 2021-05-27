<?php

namespace frontend\controllers;

use common\models\Customer;
use dektrium\user\controllers\AdminController;
use frontend\models\PaymentMethod;
use Yii;

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

    public $customers = [];


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
             * @todo This should return same data structure for both cases! Otherwise, it is half useful. (Vitaliy)
             */
            if (!Yii::$app->user->identity->isAdmin) {
                $this->customers = Yii::$app->user->identity->customerIds;
            }else {
                $this->customers = Customer::getList();
            }
        }
    }

}
