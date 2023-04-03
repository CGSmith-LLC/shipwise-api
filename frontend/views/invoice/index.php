<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $paymentMethodDataProvider yii\data\ActiveDataProvider */
/* @var $invoiceDataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="payment-method-index">

    <div style="padding: 40px">
        <div>
            <h2 style="text-align:center">Invoices</h2>
        </div>
        <?php

        // Invoices
        echo GridView::widget([
            'dataProvider' => $invoiceDataProvider,
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'Invoice #',
                ],
                'customer_name',
                [
                    'attribute' => 'amount',
                    'value' => fn($model) => Yii::$app->formatter->asCurrency($model->decimalAmount),
                ],
                'due_date:date',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => fn($model) => $model->getStatusLabel()
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'urlCreator' => fn($action, $model, $key, $index) => \yii\helpers\Url::toRoute(['invoice/view', 'id' => $model->id]),
                ],
            ],
        ]); ?>
    </div>
</div>
