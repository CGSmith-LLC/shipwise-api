<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\SubscriptionForm */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\DatePickerAsset::register($this);

$item = new \frontend\models\SubscriptionItems();
$item->loadDefaultValues();

$this->registerJs("$(function () {
    $('#datetimepicker').datepicker();
});", \yii\web\View::POS_READY);


$this->registerJs('
            $("#btn-add-item").off(\'click\').on("click", addItem);

            // remove item button
            $(document).on(\'click\', \'.btn-remove-item\', function () {
                $(this).closest(\'.row\').remove();
            });', \yii\web\View::POS_READY);

// Trigger btn-add-item button to add first item if order is a new record.
if (!Yii::$app->request->isPost && $model->subscription->isNewRecord) {
    $this->registerJs("$('#btn-add-item').click();");
}

$this->registerJsFile('@web/js/subscription-create.js');


?>

<div class="subscription-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false,]); ?>

    <?= $form->field($model->subscription, 'customer_id')->dropDownList(\frontend\models\Customer::getList())->label('Customer Name')  ?>

    <?= $form->field($model->subscription, 'next_invoice')->textInput([
        'id' => 'datetimepicker',
    ])->label('Next Invoice Date') ?>

    <?= $form->field($model->subscription, 'months_to_recur')->textInput() ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Subscription Items</h3>
                </div>
                <div class="panel-body">

                    <div id="items">
                        <?php
                        // existing item fields
                        foreach ($model->items as $itemKey => $_item) : ?>
                            <div class="row item item-<?= $itemKey ?>">
                                <?= $this->render('partial/_item-row', [
                                    'key'     => $_item->isNewRecord
                                        ? (strpos($itemKey, 'new') !== false ? $itemKey : 'new' . $itemKey)
                                        : $_item->id,
                                    'form'    => $form,
                                    'item'    => $_item,
                                    'counter' => $itemKey,
                                ]); ?>
                            </div>
                        <?php endforeach; ?>

                        <div id="new-item-block" class="row hidden">
                            <?= $this->render('partial/_item-row', [
                                'key'     => '__id__',
                                'form'    => $form,
                                'item'    => $item,
                                'counter' => 0,
                            ]);
                            ?>
                        </div>
                    </div>

                    <?= Html::a('<i class="glyphicon glyphicon-plus"></i> Add Another Item', 'javascript:void(0);', [
                        'id'    => 'btn-add-item',
                        'class' => 'btn btn-warning btn-sm',
                    ]) ?>

                </div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
