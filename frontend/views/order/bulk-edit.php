<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\BulkEditForm */
/* @var $customers array List of customers */
/* @var $statuses array List of order statuses */

$this->title = 'Bulk Order Edit';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\frontend\assets\ToggleAsset::register($this);
$status = \common\models\Status::ON_HOLD;
$js = <<<JS

$("#status-dropdown").on('change', function () {
    if (this.value == {$status}) {
        $('#reopen-dialog').show();
    } else {
        $('#bulkeditform-reopen_enable').prop('checked', false).change()
        $('#reopen-dialog').hide();
    }
});

$("#bulkeditform-reopen_enable").on('change', function() {
    if ($(this).prop('checked')) {
        $('#reopen-date').show();
    }else {
        $('#reopen-date').hide();
    }
})
JS;

$this->registerJs($js);

\frontend\assets\DatePickerAsset::register($this);
$this->registerJs('
    // Datepicker
    $(\'.date\').flatpickr({
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y H:i",    
    minDate: "today",
})');
?>
<div class="order-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-bulk-edit',
    ]); ?>
    <div class="panel-body">

        <?= $form->field($model, 'action')
            ->dropdownList($statuses, [
                'prompt' => '-- Unknown --',
                'readonly' => $confirmed,
                'id' => 'status-dropdown'
                ]); ?>

        <div id="reopen-dialog" style="<?= (!$model->reopen_enable) ? 'display: none' : ''; ?>">
            <label>Do you want to automatically change the status to Open at a specific time?</label>
            <?= $form->field($model, 'reopen_enable')->checkbox([
                  'readonly' => $confirmed,
                  'data-toggle' => 'toggle',
                  'data-on' => 'Yes',
                  'data-off' => 'No',
                  'label' => false,
              ]); ?>

            <div id="reopen-date" style="<?= (!$model->reopen_enable) ? 'display: none' : ''; ?>">
                <?= $form->field($model, 'open_date', [
                    'inputOptions' => ['autocomplete' => 'off']
                ])->textInput([
                    'readonly' => $confirmed,
                    'class' => 'date',
                    'value' => $model->open_date ?? '',
                ])->label('Date and time to automatically change orders to Open'); ?>
            </div>
        </div>

        <?= $form->field($model, 'customer')
            ->dropdownList($customers, ['prompt' => ' Please select', 'readonly' => $confirmed,]); ?>
        <?= $form->field($model, 'order_ids')
            ->textArea(['rows' => 10, 'readonly' => $confirmed])
            ->hint('Paste in a list of customer order #\'s separated by spaces, commas, or line breaks'); ?>
        <?= $form->field($model, 'confirmed')->hiddenInput()->label(false); ?>
        <?= Html::submitButton(($confirmed) ? 'Save Changes' : 'Review &rarr;', ['class' => 'btn btn-lg btn-success']); ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
