<?php

/* @var $model PaymentMethod */
/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\models\PaymentMethod;
use yii\widgets\ActiveForm;

\Stripe\Stripe::setApiKey(Yii::$app->stripe->privateKey);

$this->registerJsFile('@web/cc-form.js');
$this->registerJsFile('https://js.stripe.com/v3/');

?>

<div class="payment-method-form">
    <?php $form = \yii\widgets\ActiveForm::begin(['id' => 'add-cc-form']); ?>

    <div class="form-row">
        <label for="card-element">
            Credit or Debit Card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>
    </div>

    <?= $form->field($model, 'default')->checkbox() ?>
    <?= $form->field($model, 'stripe_payment_method_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Save Card'), ['class' => 'btn btn-success', 'onclick' => 'createCC()', 'data-secret' => $model->setupIntent->client_secret, 'Id' => 'card-button'])?>
    </div>



    <input id="cardholder-name" type="text">
    <!-- placeholder for Elements -->

    <?php ActiveForm::end(); ?>

</div>
