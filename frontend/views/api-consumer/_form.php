<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiConsumer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-consumer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'auth_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auth_secret')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'auth_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_activity')->textInput() ?>

    <?= $form->field($model, 'customer_id')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'created_date')->textInput() ?>

    <?= $form->field($model, 'superuser')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
