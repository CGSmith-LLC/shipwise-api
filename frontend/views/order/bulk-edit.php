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
                ]); ?>

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