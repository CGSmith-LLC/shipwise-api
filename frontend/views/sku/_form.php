<?php

use frontend\assets\ToggleAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sku */
/* @var $form yii\widgets\ActiveForm */
/* @var $customers array of customer ids */

ToggleAsset::register($this);

?>

<p>Items/SKUs can be included or excluded from Shipwise. You do not need to have any SKUs here for
    Shipwise to work properly. If you have a SKU of <strong>GIFTCARD</strong> that you do not want to be sent to fulfillment
    or to be excluded from orders then you should create a SKU and select the <i>excluded</i> option. This is known as an
    <i>exclusion</i> list.<br/><br/>

    If you want an <i>inclusion</i> list then you should create all of the SKUs that should be sent to fulfillment and make
    sure the <i>excluded</i> option is 'No'.</p>
<div class="sku-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <label>Should Shipwise exclude this item from fulfillment?</label>
    <?= $form->field($model, 'excluded')->checkbox([
        'data-toggle' => 'toggle',
        'data-on' => 'Yes',
        'data-off' => 'No',
        'label' => false,
    ]); ?>

    <?= $form->field($model, 'customer_id')->dropDownList($customers, ['prompt' => 'Please Select']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
