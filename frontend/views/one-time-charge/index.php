<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'One Time Charges';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="one-time-charge-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create One Time Charge', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'customer_id',
            'name',
            [
                'attribute' => 'decimalAmount',
                'format' => 'currency',
            ],
            'added_to_invoice',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
