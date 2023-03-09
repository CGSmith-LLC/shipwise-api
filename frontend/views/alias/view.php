<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AliasParent */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Aliases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="alias-parent-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create New Alias', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary row">
                <div class="panel-heading">
                    <p class="panel-title">Alias</p>
                </div>
                <div class="col-md-3">
                    <label class="control-label">Customer</label>
                    <p><?= $model->customer->name; ?></p>
                </div>
                <div class="col-md-3">
                    <label class="control-label">SKU</label>
                    <p><?= $model->sku; ?></p>
                </div>
                <div class="col-md-3">
                    <label class="control-label">Name</label>
                    <p><?= $model->name; ?></p>
                </div>
                <div class="col-md-3">
                    <label>Is this alias active?</label>
                    <p><?= $model->active ? 'Yes' : 'No'; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary row">
                <div class="panel-heading">
                    <p class="panel-title">Child Items</p>
                </div>

                <?php foreach ($model->items as $key => $item) { ?>
                    <div class="item">
                        <div class="col-md-4">
                            <?php if ($key === 0) { ?><label class="control-label">Quantity</label><?php } ?>
                            <p><?=$item->quantity?></p>
                        </div>
                        <div class="col-md-4">
                            <?php if ($key === 0) { ?><label class="control-label">SKU</label><?php } ?>
                            <p><?=$item->sku?></p>
                        </div>
                        <div class="col-md-4">
                            <?php if ($key === 0) { ?><label class="control-label">Name</label><?php } ?>
                            <p><?=$item->name?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
