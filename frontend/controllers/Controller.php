<?php

namespace frontend\controllers;

use dektrium\user\controllers\AdminController;
use Yii;

/**
 * Class Controller
 * Base controller responsible for checking if the user has any payment methods
 *
 * @package frontend\controllers
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


    public function init()
    {
        // Only perform this check if a user is logged in
        if (!Yii::$app->user->isGuest) {
            /**
             * Check for conditions 1 2 3 4 5 6
             *
             *
             */
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
        }

        parent::init();
    }

}