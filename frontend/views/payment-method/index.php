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

    <div class="row">
    <?php
        // Display card information
        // Iterate over cards, ask stripe for the payment method details
        /// Display last 4, expiration, and if its default

        /** @var \frontend\models\PaymentMethod $paymentMethod */
        foreach ($paymentMethodDataProvider->getModels() as $paymentMethod) {
            $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethod->stripe_payment_method_id);
            ?>
            <div class="col-12">
                <div><?= $stripePaymentMethod->card->brand; ?> <?php
                    if ($paymentMethod->default === \common\models\PaymentMethod::PRIMARY_PAYMENT_METHOD_YES) { ?>
                    <span class="label label-primary">Default</span>
                    <?php
                    }
                    ?>
                </div>
                <div>**** <?=$stripePaymentMethod->card->last4; ?></div>
                <div><?=$stripePaymentMethod->card->exp_month . '/' . $stripePaymentMethod->card->exp_year; ?></div>
                <div>Delete Make Default</div>
            </div>
        <?php
        }
    ?>
        <p style="text-align: right">
            <?= Html::a('Add a Credit Card ', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
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
