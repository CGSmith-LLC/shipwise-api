<?php

use frontend\assets\ToggleAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Webhook */
/* @var $form yii\widgets\ActiveForm */

ToggleAsset::register($this);
?>

<div class="webhook-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->label('Customer Name')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'endpoint')->label('HTTPS Endpoint')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'authentication_type')->dropDownList($model->authenticationOptions) ?>

    <?= $form->field($model, 'user')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass')->passwordInput(['maxlength' => true]) ?>


    <label>Active</label>
    <?= $form->field($model, 'active')->checkbox([
        'data-toggle' => 'toggle',
        'data-on' => 'Yes',
        'data-off' => 'No',
        'label' => false,
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
