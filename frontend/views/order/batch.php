<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array List of order statuses */

$this->title = 'Batches';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="order-batch">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= GridView::widget([
        'id' => 'orders-grid-view',
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            [
                'attribute' => 'name',
                'value' => function ($model) {
                    return Html::a($model->name, \yii\helpers\Url::toRoute(['order/batch', 'id' => $model->id]));
                },
                'format' => 'raw',
            ]
        ],
    ]); ?>
</div>
