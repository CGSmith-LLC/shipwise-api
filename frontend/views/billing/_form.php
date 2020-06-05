<?php

/* @var $model PaymentMethod */
/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\models\PaymentMethod;
use yii\widgets\ActiveForm;

\Stripe\Stripe::setApiKey(Yii::$app->stripe->privateKey);

$this->registerJsFile('@web/js/cc-form.js');
$this->registerJsFile('https://js.stripe.com/v3/');
$this->registerCssFile('web/css/site.css')
?>

<div class="payment-method-form">
    <?php $form = \yii\widgets\ActiveForm::begin(['id' => 'add-cc-form']); ?>


    <div class="form-row">
        <label for="cardholder-name">Name</label>
        <input id="cardholder-name" type="text">
    </div>
    <div class="form-row">
        <label for="card-number">Credit or Debit Card</label>
        <div id="card-number"></div>
        <div id="card-errors" role="alert"></div>
    </div>
    <div class="form-row">
        <label for="card-expiration">Expiration</label>
        <div id="card-expiration"></div>
        <div id="card-errors" role="alert"></div>
    </div>
    <div class="form-row">
        <label for="card-cvc">CVC</label>
        <div id="card-cvc"></div>
        <div id="card-errors" role="alert"></div>
    </div>

    <?= $form->field($model, 'default')->checkbox() ?>
    <?= $form->field($model, 'stripe_payment_method_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Save Card'), ['class' => 'btn btn-success', 'onclick' => 'createCC()', 'data-secret' => $model->setupIntent->client_secret, 'Id' => 'card-button'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


