<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sku */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array of customer ids */
?>

<div class="sku-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'substitute_1')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'substitute_2')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'substitute_3')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_id' )->dropDownList($customers, ['prompt' => 'Please Select'])?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
