<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Subscription */

$this->title = $model->customer->name . ' Subscription #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Subscriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="subscription-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'next_invoice',
            'months_to_recur',
        ],
    ]) ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            [
                'attribute' => 'amount',
                'value' => function ($model) {
                    return Yii::$app->formatter->asCurrency($model->decimalAmount);
                }
            ],
        ]
    ]) ?>

</div>
