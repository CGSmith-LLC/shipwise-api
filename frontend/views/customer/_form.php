<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\assets\ToggleAsset;


/* @var $this yii\web\View */
/* @var $model common\models\Customer */
/* @var $form yii\widgets\ActiveForm */
/* @var $states array List of states */

ToggleAsset::register($this);
?>

<div class="customer-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'state_id')
        ->dropdownList($states, ['prompt' => ' Please select']) ?>

    <?= $form->field($model, 'zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
<label>Direct Customer?</label>
    <?= $form->field($model, 'direct')->checkbox([
        'data-toggle' => 'toggle',
        'data-on' => 'Yes',
        'data-off' => 'No',
        'label' => false,
    ]); ?>
    
    <?= $form->field($model, 'imageFile')->fileInput()?>

    <?= $form->field($model, 'stripe_customer_id')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
