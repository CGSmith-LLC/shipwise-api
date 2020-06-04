<?php

namespace frontend\controllers;

/**
 * Class Controller
 * Base controller responsible for checking if the user has any payment methods
 *
 * @package frontend\controllers
 */
class Controller extends \yii\web\Controller
{
    public function init()
    {
        if (!\Yii::$app->user->identity->isAdmin &&
            !\Yii::$app->user->identity->getCustomerStripeId() &&
            $this->module->requestedRoute !== 'billing/create') {
            \Yii::$app->getSession()->setFlash('error', 'Please Set Up Payment Method and Add A Card To Your Profile.');
            //Cant return because init function does not return.
            $this->redirect(['/billing/create', 'status' => 'not-setup']);
            //Need to stop the app from continuing to display the Flash Message.
            \Yii::$app->end();

        }
        parent::init();
    }

}