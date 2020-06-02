<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $paymentMethodDataProvider yii\data\ActiveDataProvider */
/* @var $invoiceDataProvider yii\data\ActiveDataProvider */

$this->title = 'Billing';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="payment-method-index">

    <h1 style="text-align:center"><?= Html::encode($this->title) ?></h1>

    <div class="container" style="border-style: solid">
        <?php
        // Display card information
        // Iterate over cards, ask stripe for the payment method details
        /// Display last 4, expiration, and if its default

        /** @var \frontend\models\PaymentMethod $paymentMethod */
        foreach ($paymentMethodDataProvider->getModels() as $paymentMethod) {
            $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethod->stripe_payment_method_id);
            ?>
            <div class = text-center style="float: left">
                <div style="padding: 25px">Card Type : <?= $stripePaymentMethod->card->brand; ?></div>
                <div style="padding: 20px">**** <?= $stripePaymentMethod->card->last4; ?></div>
                <div style="padding: 20px">Exp Date - <?= $stripePaymentMethod->card->exp_month . '/' . $stripePaymentMethod->card->exp_year; ?></div>
                <div style="padding: 20px" >
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
                    }
                    ?>

                    <div>
                        <?php
                        //Mark the card as default for payment
                       //echo Html::a('Make Default', ['select', 'id' => $paymentMethod->id]);
                        if ($paymentMethod->default === \common\models\PaymentMethod::PRIMARY_PAYMENT_METHOD_YES) { ?>
                            <span class="btn btn-md btn-primary" disabled="disabled">Default</span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <p  style= "text-align: center">
            <?= Html::a('Add a Credit Card ', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <div style="padding: 40px">
    <div>
        <h2 style="text-align:center">Invoices</h2>
    </div>
        <?php

    // Invoices
    echo GridView::widget([
        'dataProvider' => $invoiceDataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'customer_name',
            'subscription_id',
            'amount',
            'balance',
            'due_date',
            'status',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>
</div>
