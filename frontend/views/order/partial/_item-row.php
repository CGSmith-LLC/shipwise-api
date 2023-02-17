<?php
/**
 * Partial view for item row
 *
 * @see \frontend\controllers\OrderController::actionCreate()
 *
 * @var yii\web\View                    $this
 * @var yii\widgets\ActiveForm          $form    The form
 * @var common\models\forms\OrderForm $model   The order form model
 * @var frontend\models\Item            $item    The item
 * @var int                             $key     Item key
 * @var int                             $counter Item counter
 */

use yii\helpers\Html;

// custom template for item fields
$fieldTemplate = '<div style="margin:0 10px"><label class="control-label">{label}</label>{input}{hint}{error}</div>';
?>

<div class="col-md-2 col-sm-6 col-xs-6">
    <?= $form->field($item, 'quantity', ['template' => $fieldTemplate])
             ->textInput([
                 'id'  => "Items_{$key}_quantity",
                 'name' => "Items[$key][quantity]",
                 'placeholder' => "Quantity...",
                 'maxlength' => true,
             ])->label(false) ?>
</div>
<div class="col-md-4 col-sm-6 col-xs-6">
    <?= $form->field($item, 'sku', ['template' => $fieldTemplate])
             ->textInput([
                 'id' => "Items_{$key}_sku",
                 'name' => "Items[$key][sku]",
                 'placeholder' => "SKU...",
                 'maxlength' => true,
             ])->label(false) ?>
</div>
<div class="col-md-4 col-sm-6 col-xs-6">
    <?= $form->field($item, 'name', ['template' => $fieldTemplate])
             ->textInput([
                 'id' => "Items_{$key}_name",
                 'name' => "Items[$key][name]",
                 'placeholder' => "Name...",
                 'maxlength' => true,
             ])->label(false) ?>
</div>
<div class="col-md-2 col-sm-6 col-xs-6">
    <?= Html::a('<i class="glyphicon glyphicon-remove"></i>', 'javascript:void(0);', [
        'class' => 'btn-remove-item btn btn-danger btn-xs',
        'style' => 'margin-top: 25px',
        'title' => 'Remove',
    ]) ?>
</div>
