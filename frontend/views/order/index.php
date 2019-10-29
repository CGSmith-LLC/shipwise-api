<?php

use frontend\models\Customer;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array List of order statuses */

$this->title                   = 'Orders';
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

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="row">
        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">
            <?= Html::dropDownList('OrderSearch[pageSize]', $searchModel->pageSize,
                $searchModel->pageSizeOptions,
                [
                    'id'             => 'ordersearch-pagesize',
                    'class'          => 'form-control',
                    'data-toggle'    => 'tooltip',
                    'data-placement' => 'right',
                    'title'          => '# of entries to show per page',
                ]) ?>
        </div>
        <div class="col-lg-11">
            <div class="pull-right">
                <?= Html::a('<span class="glyphicon glyphicon-remove-sign"></span> Clear filters', [''],
                    ['class' => 'btn btn-default btn-xs']) ?>
            </div>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider'   => $dataProvider,
        'filterModel'    => $searchModel,
        'filterSelector' => '#' . Html::getInputId($searchModel, 'pageSize'),
        'pager'          => [
            'firstPageLabel' => Yii::t('app','First'),
            'lastPageLabel'  => Yii::t('app','Last'),
        ],
        'columns'        => [
            [
                'attribute' => 'customer.name',
                // Visible if admin or has a count higher than 0 for associated users
                'visible'   => ((count($customerDropdownList) > 1) || Yii::$app->user->identity->getIsAdmin()),
                'filter'    => Html::activeDropDownList(
                    $searchModel,
                    'customer_id',
                    $customerDropdownList,
                    ['class' => 'form-control', 'prompt' => Yii::t('app', 'All Customers')]
                ),
            ],
            'customer_reference',
            [
                'attribute' => 'address',
                'value'     => 'address.name',
            ],
            //'requested_ship_date:date',
            'tracking',
            'created_date:datetime',
            //'updated_date',
            //'notes',
            //'uuid',
            //'carrier_id',
            //'service_id',
            //'origin',
            [
                'attribute' => 'status_id',
                'options'   => ['width' => '10%'],
                'value'     => 'status.name',
                'filter'    => Html::activeDropDownList(
                    $searchModel,
                    'status_id',
                    $statuses,
                    ['class' => 'form-control', 'prompt' => Yii::t('app', 'All')]
                ),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
