<?php

use frontend\models\forms\IntegrationForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IntegrationForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array of customers */
/* @var $ecommercePlatforms array of platforms */

?>

<div class="integration-form">

    <?php $form = ActiveForm::begin([
        'id' => 'integration-form'
    ]); ?>

    <?php
    if (Yii::$app->request->post()) {
        $model->errorSummary($form);
        die;
    }
    ?>
    <?php ?>

    <?= $form->field($model, 'customer_id')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <?= $form->field($model, 'name')->input('text'); ?>
    <?= $form->field($model, 'ecommerce')->dropdownList($ecommercePlatforms, ['disabled' => !$model->isNewRecord, 'prompt'   => ' -- Unknown --',]) ?>

    <div class="form-group">
        <?= Html::submitButton('Next &rarr;', ['class' => 'btn btn-lg btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
