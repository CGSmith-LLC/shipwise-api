<?php

use frontend\assets\DatePickerAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \frontend\models\forms\ReportForm */
/* @var $customers array of customers */

DatePickerAsset::register($this);
$this->registerJs('
            // Datepicker
            $(\'.date\').datepicker({
                todayBtn           : \'linked\',
                keyboardNavigation : false,
                forceParse         : false,
                autoclose          : true,
                format             : \'mm/dd/yyyy\',
                todayHighlight     : true,
            });');
$this->title = Yii::$app->name;
?>
<div class="report-index">

    <div class="body-content">
        <h1>Reports</h1>
        <p>Choose the date range below. We will generate a CSV file for you after clicking export.</p>
        <div class="report-form">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'start_date', [
                'inputTemplate' =>
                    '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
                'inputOptions' => ['autocomplete' => 'off']
            ])->textInput([
                'value' => (isset($model->start_date)) ? Yii::$app->formatter->asDate($model->start_date) : '',
            ]); ?>
            <?= $form->field($model, 'end_date', [
                'inputTemplate' =>
                    '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
                'inputOptions' => ['autocomplete' => 'off']
            ])->textInput([
                'value' => (isset($model->end_date)) ? Yii::$app->formatter->asDate($model->end_date) : '',
            ]); ?>
            <?php echo $form->field($model, 'customer')->dropdownList($customers, ['prompt' => ' Please select']); ?>

            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
