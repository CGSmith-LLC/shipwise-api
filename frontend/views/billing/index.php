<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $paymentMethodDataProvider yii\data\ActiveDataProvider */
/* @var $invoiceDataProvider yii\data\ActiveDataProvider */

$this->title = 'Billing';
$this->registerCssFile('/css/card.css')


?>
<div class="payment-method-index">

    <h1 style="text-align:center">Payment Methods</h1>
    <table class="table table-hover table-bordered">
        <?php
        // Display card information
        // Iterate over cards, ask stripe for the payment method details
        /// Display last 4, expiration, and if its default

        /** @var \frontend\models\PaymentMethod $paymentMethod */

        foreach ($paymentMethodDataProvider->getModels() as $paymentMethod) {
        ?>
            <tr>

                <td><?php
                    echo $paymentMethod->brandImage() ;

                    if ($paymentMethod->default) {
                        echo '<span class="label label-primary" disabled="disabled">Default</span>';
                    }
                    ?>
                </td>
                <td>****<?= $paymentMethod->lastfour; ?></td>
                <td><?= $paymentMethod->expiration; ?></td>
                <td>
                    <?php
                    // If it is not default allow deletion
                    if ($paymentMethod->default === \common\models\PaymentMethod::PRIMARY_PAYMENT_METHOD_NO) {
                        echo Html::a('Delete', ['delete', 'id' => $paymentMethod->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this payment method?',
                                'method' => 'post',
                            ],
                        ]);
                    } else {
                        echo Html::button('Delete', [
                            'class' => 'btn btn-danger',
                            'disabled' => true,
                        ]);
                    }
                    ?>
                    <?php
                    //Mark the card as default for payment
                    if ($paymentMethod->default != \common\models\PaymentMethod::PRIMARY_PAYMENT_METHOD_YES) {

                        echo Html::a('Make Default', ['select', 'id' => $paymentMethod->id], [
                            'class' => 'btn btn-default',
                            'data' => [
                                'confirm' => 'Are you sure you make this you default payment method?',
                                'method' => 'post',
                            ],
                        ]);
                    }
                    ?>
                </td>
            </tr>

            <?php
        }
        ?>
        <p style="text-align: center">
            <?= Html::a('Add a Credit Card ', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </table>


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
                    'value' => function ($model) {
                        return Yii::$app->formatter->asCurrency($model->decimalAmount);
                    },
                ],
                'due_date:date',
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->getStatusLabel();
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'urlCreator' => function($action, $model, $key, $index) {
                        return \yii\helpers\Url::toRoute(['invoice/view', 'id' => $model->id]);
                    },
                ],
            ],
        ]); ?>
    </div>
</div>
