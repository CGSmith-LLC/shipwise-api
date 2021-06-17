<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Api Consumers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-consumer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Api Consumer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'auth_key',
            'auth_secret',
            'auth_token',
            'last_activity',
            //'customer_id',
            //'status',
            //'created_date',
            //'superuser',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
