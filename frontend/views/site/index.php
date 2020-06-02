<?php

use frontend\assets\DatePickerAsset;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::$app->name;
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

Modal::begin([
    'id' => 'dashboardSearch',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
    'header' => '<h4>Search</h4>',
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>',
]); ?>
    <div class="report-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'start_date', [
            'inputTemplate' =>
                '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
        ])->textInput([
            'value' => (isset($model->start_date)) ? Yii::$app->formatter->asDate($model->start_date) : '',
        ]); ?>
        <?= $form->field($model, 'end_date', [
            'inputTemplate' =>
                '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
        ])->textInput([
            'value' => (isset($model->end_date)) ? Yii::$app->formatter->asDate($model->end_date) : '',
        ]); ?>
        <?php echo $form->field($model, 'customers')->dropdownList($customers, ['prompt' => ' Please select']); ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php
Modal::end();

$this->registerJsFile('js/dashboard-search.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJs('
 $("#searchButton").click(function () {
    dashboardSearch();
 });
');
?>
<div class="body-content">
    <h1>Dashboard <button id="searchButton" class="btn btn-primary">Search</button></h1>

</div>
<div class="row">
    <div class="col-md-3 col-xl-3">
        <div class="card bg-c-shipwise order-card">
            <div class="card-block">
                <h3>Open Orders</h3>
                <h2 class="text-right"><i class="fa fa-arrow-circle-down f-left"></i><span><?=$openCount; ?></span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xl-3">
        <div class="card bg-c-yellow order-card">
            <div class="card-block">
                <h3>Pending Fulfillment</h3>
                <h2 class="text-right"><i class="fa fa-cart-plus f-left"></i><span><?=$pendingCount; ?></span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xl-3">
        <div class="card bg-c-green order-card">
            <div class="card-block">
                <h3>Shipped</h3>
                <h2 class="text-right"><i class="fa fa-rocket f-left"></i><span><?=$shippedCount; ?></span></h2>
            </div>
        </div>
    </div>


    <div class="col-md-3 col-xl-3">
        <div class="card bg-c-blue order-card">
            <div class="card-block">
                <h3>Completed</h3>
                <h2 class="text-right"><i class="fa fa-check f-left"></i><span><?=$completedCount; ?></span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-xl-3">
        <div class="card bg-c-pink order-card">
            <div class="card-block">
                <h3>Error</h3>
                <h2 class="text-right"><i class="fa fa-times f-left"></i><span><?=$errorCount; ?></span></h2>
            </div>
        </div>
    </div>
</div>