<?php

use frontend\assets\DatePickerAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\forms\ReportForm */
/* @var $form ActiveForm */

?>

<?php $form = ActiveForm::begin(); ?>

    <p>
        Enter a list of order numbers below.
        Order numbers can be separated by <code>,</code>, <code>;</code> or spaces and newlines.<br>
        We will generate a CSV file for you after clicking export.
    </p>

    <?= Html::hiddenInput('scenario', \frontend\models\forms\ReportForm::SCENARIO_BY_ORDER_NR) ?>

    <?php echo $form->field($model, 'order_nrs')->textarea(['rows' => 10]) ?>

    <?php echo $form->field($model, 'items')->checkbox(['id' => 'include_items_order_nr'])->label('Include items in report?'); ?>

    <div class="form-group">
        <?= Html::submitButton('Export', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
