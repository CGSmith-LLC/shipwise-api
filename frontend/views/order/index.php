<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_id',
            'order_reference',
            'customer_reference',
            'status_id',
            //'tracking',
            //'created_date',
            //'updated_date',
            //'address_id',
            //'notes',
            //'uuid',
            //'requested_ship_date',
            //'carrier_id',
            //'service_id',
            //'origin',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
