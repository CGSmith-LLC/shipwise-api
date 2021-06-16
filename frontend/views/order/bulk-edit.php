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

Yii::debug($statuses);
?>

<div class="order-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-bulk-edit',
        'enableClientValidation' => false,
    ]); ?>
    <div class="panel-body">

        <?= $form->field($model, 'action')
            ->dropdownList($statuses, [
                'prompt' => '-- Unknown --',
            ]) ?>

        <?= $form->field($model, 'customer')
            ->dropdownList($customers, ['prompt' => ' Please select']) ?>

        <?= $form->field($model, 'delimiter')
             ->radioList(['spaces' => 'Spaces', 'commas' => 'Commas', 'newlines' => 'Newlines'])   ?>

        <?= $form->field($model, 'order_ids')->textArea(['rows' => 10])->hint('Paste in a list of customer order #\'s separated by spaces, commas, or line breaks') ?>


    </div>

</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Review &rarr;', ['class' => 'btn btn-lg btn-success']) ?>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>
