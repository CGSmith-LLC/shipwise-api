<?php

use frontend\assets\ToggleAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sku */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array of customer ids */

ToggleAsset::register($this);

?>

<div class="sku-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <label>Is This An Excluded Item?</label>
    <?= $form->field($model, 'excluded')->checkbox([
        'data-toggle' => 'toggle',
        'data-on' => 'Yes',
        'data-off' => 'No',
        'label' => false,
    ]); ?>

    <?= $form->field($model, 'substitute_1')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'substitute_2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'substitute_3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_id')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
