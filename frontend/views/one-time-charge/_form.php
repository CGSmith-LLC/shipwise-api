<?php

use frontend\assets\ToggleAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\OneTimeCharge */
/* @var $form yii\widgets\ActiveForm */

ToggleAsset::register($this);
?>

<div class="one-time-charge-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model); ?>
    <?= $form->field($model, 'customer_id')->dropDownList(\frontend\models\Customer::getList()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <label>Do you want to invoice this tomorrow's job run?</label>
    <?= $form->field($model, 'charge_asap')->checkbox([
        'data-toggle' => 'toggle',
        'data-on' => 'Yes',
        'data-off' => 'No',
        'label' => false,
    ]); ?>

    <?= $form->field($model, 'decimalAmount', [
        'template' => '{label}<div class="input-group"><span class="input-group-addon">$</span>{input}
            </div>{error}{hint}'
    ])->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
