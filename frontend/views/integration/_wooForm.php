<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\IntegrationMeta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="woo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field(\common\models\IntegrationMeta::addMeta(), 'url')->textInput() ?>

    <?= $form->field($model, 'api_key')->textInput() ?>

    <?= $form->field($model, 'api_secret')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
