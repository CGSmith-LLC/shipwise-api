<?php
/**
 * Partial view for item row
 *
 * @see \frontend\controllers\OrderController::actionCreate()
 *
 * @var yii\web\View                    $this
 * @var yii\widgets\ActiveForm          $form    The form
 * @var common\models\forms\OrderForm   $model   The order form model
 * @var common\models\AliasChildren   $item    The item
 * @var int                             $key     Item key
 * @var int                             $counter Item counter
 */

use yii\helpers\Html;

// custom template for item fields
$fieldTemplate = '<div><label class="control-label">{label}</label>{input}{hint}{error}</div>';
?>

<div class="col-md-3">
    <?= $form->field($item, 'quantity', ['template' => $fieldTemplate])
             ->textInput([
                 'id'        => "AliasChildren_{$key}_quantity",
                 'name'      => "AliasChildrenQty[]",
                 'maxlength' => true,
             ])->label($counter === 0 ? $item->getAttributeLabel('quantity') : false) ?>
</div>
<div class="col-md-3">
    <?= $form->field($item, 'sku', ['template' => $fieldTemplate])
             ->textInput([
                 'id'        => "AliasChildren_{$key}_sku",
                 'name'      => "AliasChildrenSku[]",
                 'maxlength' => true,
             ])->label($counter === 0 ? $item->getAttributeLabel('sku') : false) ?>
</div>
<div class="col-md-4">
    <?= $form->field($item, 'name', ['template' => $fieldTemplate])
             ->textInput([
                 'id'        => "AliasChildren_{$key}_name",
                 'name'      => "AliasChildrenName[]",
                 'maxlength' => true,
             ])->label($counter === 0 ? $item->getAttributeLabel('name') : false) ?>
</div>

<div class="col-md-1">
    <div class="form-group">
        <label class="control-label fake" style="color: #fff" for="btn-danger">remove</label>
        <?= Html::a('<i class="glyphicon glyphicon-remove"></i>', 'javascript:void(0);', [
            'class' => 'btn-remove-item btn btn-danger btn-xs',
            'title' => 'Remove',
        ]) ?>
    </div>
</div>
