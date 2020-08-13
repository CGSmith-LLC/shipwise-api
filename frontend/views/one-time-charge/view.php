<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\OneTimeCharge */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'One Time Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="one-time-charge-view">

    <?php
    $dataProviderHistory = new \yii\data\ActiveDataProvider([
        'query' => \common\models\OrderHistory::find()->where(['order_id' => $model->id])
    ]);

    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => \frontend\models\Item::find()->where(['order_id' => $model->id]),
    ]);
    ?>

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
            'id',
            'customer.name',
            [
                'attribute' => 'name',
                'label' => 'One Time Charge Name'
            ],
            [
                'attribute' => 'decimalAmount',
                'format' => 'currency',
            ],
            [
                'attribute' => 'added_to_invoice',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model ->added_to_invoice) {
                        return '<p class="label label-success">Invoiced</p>';
                    } else {
                        return '<p class="label label-default">Pending</p>';
                    }
                }
            ],
            [
                'attribute' => 'charge_asap',
                'label' => 'Charged on next invoice run?',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model ->charge_asap) {
                        return '<p class="label label-primary">Yes</p>';
                    } else {
                        return '<p class="label label-default">No</p>';
                    }
                }
            ],
        ],
    ]) ?>


</div>
