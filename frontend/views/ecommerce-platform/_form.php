<?php
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $model EcommercePlatform */
/* @var $form ActiveForm */

$statusLabel = '<span><i class="glyphicon glyphicon-info-sign" title="If you make the platform inactive, in this case, all integrations with the platform will be paused."></i> Status</span>';
$metaDataLabel = '<span><i class="glyphicon glyphicon-info-sign" title="Use this field for storing any additional data for the platform in JSON format."></i> Meta Data</span>';
?>

<?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-sm-6">
        <?= $form->field($model, 'name')
            ->textInput(['maxlength' => true, 'disabled' => true, 'required' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'status')
            ->dropdownList(EcommercePlatform::getStatuses(), ['prompt' => ' Status', 'required' => true])
            ->label($statusLabel) ?>

    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'meta')
            ->textarea(['rows' => 6])
            ->label($metaDataLabel) ?>
    </div>
</div>

<div class="form-group">
    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
