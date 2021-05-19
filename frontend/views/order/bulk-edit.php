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
        'enableClientValidation' => false,
    ]); ?>
    <div class="panel-body">

        <?= $form->field($model->action, 'action')
            ->dropdownList($statuses, [
                'prompt' => ' -- Unknown --',
            ]) ?>

        <?= $form->field($model->customer, 'customer')
            ->dropdownList($customers, ['prompt' => ' Please select']) ?>

        <?= $form->field($model->action_variables, 'action_variables')->textInput(['maxlength' => true]) ?>


    </div>

</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Verify?', ['class' => 'btn btn-lg btn-success']) ?>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>

