<?php

use frontend\models\{Customer};
use yii\helpers\{Html};
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Scheduled Orders';
$this->params['breadcrumbs'][] = $this->title;
/**
 * - Get a dropdown list from the associated customers
 * - OR - get a dropdown list of all customers if admin
 */
if ((!Yii::$app->user->identity->getIsAdmin())) {
    $customerDropdownList = Yii::$app->user->identity->getCustomerList();
} else {
    $customerDropdownList = Customer::getList();
}
?>
    <div class="order-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= GridView::widget([
            'id' => 'orders-grid-view',
            'dataProvider' => $dataProvider,
            'columns' => [
                'order.customer_reference',
                'customer.name',
                [
                    'attribute' => 'status.name',
                    'label' => 'Status will change to',
                ],
                'scheduled_date:datetime',
            ],
        ]); ?>
    </div>
