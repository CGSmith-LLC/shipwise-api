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

    <?php $form = ActiveForm::begin(); ?>

    <?php
    if (Yii::$app->request->post()) {
        $model->errorSummary($form);
        die;
    }
    ?>
    <?php ?>

    <?= $form->field($model->integration, 'customer_id')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <?= $form->field($model->integration, 'ecommerce')->dropdownList($ecommercePlatforms, ['disabled' => $model->integration->isNewRecord, 'prompt'   => ' -- Unknown --',]) ?>

    <?= $form->field($model->integration, 'fulfillment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model->metaData[0], 'key')->textInput(['name' => 'metaData[]'])?>
    <?= $form->field($model->metaData[0], 'value')->textInput(['name' => 'metaData[]'])?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-lg btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
