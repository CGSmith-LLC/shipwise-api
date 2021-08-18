<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Integration */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array of customers */

?>

<div class="integration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_id')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <?= $form->field($model, 'ecommerce')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fulfillment')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::a('Next', 'woo', ['class' => 'btn btn-success btn-sm']) ?>

        <div id="new-integration-block" class="row hidden">
            <?= $this->render('_wooForm');
            ?>
        </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
