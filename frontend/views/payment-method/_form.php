<?php


/* @var $this yii\web\View */

use yii\helpers\Html;

\Stripe\Stripe::setApiKey(Yii::$app->stripe->privateKey);

$intent = \Stripe\SetupIntent::create([
        'customer' => Yii::$app->user->identity->getCustomerStripeId(),
]);
$this->registerJsFile('@web/cc-form.js');
$this->registerJsFile('https://js.stripe.com/v3/');

?>

<div class="payment-method-form">

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
    <div class="form-group">
        <?= Html::button(Yii::t('app', 'Save Card'), ['class' => 'btn btn-success', 'onclick' => 'createCC()', 'data-secret' => $model->setupIntent->client_secret, 'id' => 'card-button']) ?>
    </div>

    <input id="cardholder-name" type="text">
    <!-- placeholder for Elements -->


</div>
