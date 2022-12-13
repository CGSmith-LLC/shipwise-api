<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Webhook */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="webhook-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'endpoint')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'authentication_type')->textInput() ?>

    <?=  Yii::$app->getSecurity()->generateRandomString();?>
    <?= $form->field($model, 'signing_secret')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'user')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'when')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
