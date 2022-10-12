<?php

use frontend\assets\AliasItemAsset;
use frontend\assets\ToggleAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\AliasParent */
/* @var $form yii\widgets\ActiveForm */

ToggleAsset::register($this);
AliasItemAsset::register($this);

$item = new \common\models\AliasChildren();
$item->loadDefaultValues();

?>


<p>Aliases are also known as bill of materials or BOMs. This is a way that a SKU on your website can be mapped
    to multiple SKUs on Shipwise. If you have a SKU of <strong>GIFTPACK</strong> and you want to ship one item of
    popcorn
    and two items of seasonings you would add the mappings for the alias right here.<br/><br/>

    You can also create an alias for use later by creating it but keeping it disabled.</p>
<div class="alias-parent-form">

    <?php
    $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary row">
                <div class="panel-heading">
                    <p class="panel-title">Alias</p>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'customer_id')->dropdownList($customers, ['prompt' => ' Please select']
                    )->label('Customer'); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'sku')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <label>Is this alias active?</label>
                    <?php
                    if ($model->isNewRecord) {
                        $model->active = true;
                    }

                    echo $form->field($model, 'active')->checkbox([
                                                                      'data-toggle' => 'toggle',
                                                                      'data-on' => 'Yes',
                                                                      'data-off' => 'No',
                                                                      'label' => false,
                                                                  ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary row">
                <div class="panel-heading">
                    <p class="panel-title">Child Items <?php
                        echo Html::a(
                            '<i class="glyphicon glyphicon-plus"></i> add another item',
                            'javascript:void(0);',
                            [
                                'id' => 'btn-add-item',
                                'class' => 'btn btn-warning btn-sm',
                            ]
                        ) ?></p>
                </div>

                <?php
                if ($model->isNewRecord) { ?>
                    <div id="new-item-block">
                        <?= $this->render('partial/_alias-row', [
                            'key' => '__id__',
                            'form' => $form,
                            'item' => $item,
                            'counter' => 0,
                        ]);
                        ?>
                    </div>
                <?php
                } else {
                    // existing item fields
                    foreach ($model->items as $itemKey => $_item) { ?>
                        <div class="item item-<?= $itemKey ?>">
                            <?= $this->render('partial/_alias-row', [
                                'key' => $_item->isNewRecord
                                    ? (strpos($itemKey, 'new') !== false ? $itemKey : 'new' . $itemKey)
                                    : $_item->id,
                                'form' => $form,
                                'item' => $_item,
                                'counter' => $itemKey,
                            ]); ?>
                        </div>
                        <?php
                    }
                } ?>
                <div id="items"></div>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end(); ?>

    <?php if (!$model->isNewRecord) { ?>
    <div id="new-item-block" style="display: none;">
        <?= $this->render('partial/_alias-row', [
            'key' => '__id__',
            'form' => $form,
            'item' => $item,
            'counter' => 1,
        ]);
        ?>
    </div>
    <?php } ?>
</div>
