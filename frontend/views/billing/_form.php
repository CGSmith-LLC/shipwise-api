<?php

/* @var $model PaymentMethod */

/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use frontend\models\PaymentMethod;

?>

<div class="payment-method-form">
    <?php $form = ActiveForm::begin([
        'id' => 'add-cc-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>


    <?= $form->field($model, 'card_number')->textInput();?>
    <?= $form->field($model, 'card_month')->textInput();?>
    <?= $form->field($model, 'card_year')->textInput();?>
    <?= $form->field($model, 'card_cvc')->textInput();?>

    <?= $form->field($model, 'default')->checkbox() ?>
    <?= $form->field($model, 'stripe_payment_method_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save Card'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


