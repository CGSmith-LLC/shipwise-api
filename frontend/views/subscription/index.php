<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Subscriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Subscription', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'customer_id',
                'value' => function ($model) {
                    return $model->customer->name;
                }
            ],
            'next_invoice',
            'months_to_recur',
            // YTD
            // OPEN invoices

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
