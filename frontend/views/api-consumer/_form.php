<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiConsumer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-consumer-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')
        ->hiddenInput(['value' => Yii::$app->user->identity->getId()]) ?>

    <?= $form->field($model, 'customer_id')
        ->dropdownList($customers, ['prompt' => ' Please select'])
        ->label(false);
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

</div>
