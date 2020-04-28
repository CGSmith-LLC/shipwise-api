<?php

namespace frontend\controllers;

use common\models\Order;
use common\models\Status;
use frontend\models\User;
use yii\web\Controller;

/**
 * Site controller
 */
class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $orders = Order::find()
            ->byStatus(Status::OPEN)
            ->forCustomers((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        $totalpendingorders = Order::find()
            ->byStatus(Status::PENDING)
            ->forCustomers((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        $shipped = Order::find()
            ->byStatus(Status::SHIPPED)
            ->forCustomers((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        $cancelled = Order::find()
            ->byStatus(Status::CANCELLED)
            ->forCustomers((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        $wmserrors = Order::find()
            ->byStatus(Status::WMS_ERROR)
            ->forCustomers((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        $completed = Order::find()
            ->byStatus(Status::COMPLETED)
            ->forCustomer((!\Yii::$app->user->identity->isAdmin) ? \Yii::$app->user->identity->getCustomerIds() : null)
            ->count();

        return $this->render('index', [
            'orders' => $orders,
            'totalpendingorders' => $totalpendingorders,
            'shipped' => $shipped,
            'cancelled' => $cancelled,
            'wmserrors' => $wmserrors,
            'completed' => $completed,
        ]);


    }


}


