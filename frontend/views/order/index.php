<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array List of order statuses */

$this->title                   = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'firstPageLabel' => 'First',
            'lastPageLabel'  => 'Last',
        ],
        'columns'        => [
            [
                'attribute' => 'id',
                'options'   => ['width' => '10%'],
            ],
            [
                'attribute' => 'customer.name',
                'options'   => ['width' => '8%'],
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
                    ['class' => 'form-control']
                ),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
