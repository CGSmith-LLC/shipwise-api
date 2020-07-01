<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'One Time Charges';

?>
<div class="one-time-charge-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create One Time Charge', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <p>
        <p class="label label-success">Invoiced</p> Has been added to the invoice and will not be added to future invoices.<br/>
        <p class="label label-default">Pending</p> Not added to an invoice yet. Should be added at next invoice run.
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'customer_id',
                'label' => 'Customer Name',
                'value' => function ($model) {
                    return $model->customer->name;
                },
            ],
            [
                'attribute' => 'name',
                'label' => 'Subscription'
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'label' => 'Total Amount',
                'value' => function($model) {
                    $return = ''; // instantiate variable
                    if ($model->added_to_invoice) {
                        $return .= '<p class="label label-success">Invoiced</p>';
                    } else {
                        $return .= '<p class="label label-default">Pending</p>';
                    }
                    $return .= ' ' . Yii::$app->formatter->asCurrency($model->decimalAmount);
                    return $return;
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
