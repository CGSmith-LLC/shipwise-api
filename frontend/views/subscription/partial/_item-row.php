<?php
/**
 * Partial view for item row
 *
 * @see \frontend\controllers\OrderController::actionCreate()
 *
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form The form
 * @var frontend\models\forms\OrderForm $model The order form model
 * @var frontend\models\Item $item The item
 * @var int $key Item key
 * @var int $counter Item counter
 */

use yii\helpers\Html;
use common\models\SubscriptionItems;

// custom template for item fields
$fieldTemplate = '<div style="margin:0 10px"><label class="control-label">{label}</label>{input}{hint}{error}</div>';
?>

<div class="col-md-4">
    <?= $form->field($item, 'name', ['template' => $fieldTemplate])
        ->textInput([
            'id' => "Items_{$key}_name",
            'name' => "Items[$key][name]",
            'maxlength' => true,
        ])->label($counter === 0 ? $item->getAttributeLabel('name') : false) ?>
</div>
<div class="col-md-2">
    <?= $form->field($item, 'decimalAmount', ['template' => $fieldTemplate])
        ->textInput([
            'id' => "Items_{$key}_amount",
            'name' => "Items[$key][amount]",
            'maxlength' => true,
        ])->label($counter === 0 ? $item->getAttributeLabel('amount') : false) ?>
</div>

<div class="col-md-1">
    <div class="">
        <label class="control-label fake" style="color: #fff" for="btn-danger">remove</label>
        <?= Html::a('<i class="glyphicon glyphicon-remove"></i>', 'javascript:void(0);', [
            'class' => 'btn-remove-item btn btn-danger btn-xs ' . (($counter === 0) ? 'hidden' : ''),
            'title' => 'Remove',
        ]) ?>
    </div>
</div>
