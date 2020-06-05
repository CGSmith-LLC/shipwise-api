<?php

/* @var $model PaymentMethod */
/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\models\PaymentMethod;
use yii\widgets\ActiveForm;

?>

<div class="payment-method-form">
    <?php $form = \yii\widgets\ActiveForm::begin(['id' => 'add-cc-form']); ?>


    <?= $form->field($model, 'card_number')->textInput(['style' => 'background-color: red']);?>
    <?= $form->field($model, 'card_month')->textInput();?>
    <?= $form->field($model, 'card_year')->textInput();?>
    <?= $form->field($model, 'card_cvc')->textInput();?>

    <?= $form->field($model, 'default')->checkbox() ?>
    <?= $form->field($model, 'stripe_payment_method_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save Card'), ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


