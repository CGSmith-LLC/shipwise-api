<?php

use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */

$this->title = 'Order ' . $model->customer_reference;

$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method'  => 'post',
            ],
        ]) ?>
        <?= Html::a('Print Packing Slip', ['packing-slip', 'id' => $model->id],
            ['class' => 'btn btn-default', 'target' => '_blank']) ?>

        <?= Html::a('Print Shipping Label', ['shipping-label', 'id' => $model->id],
            ['class' => 'btn btn-default', 'target' => '_blank']) ?>
    </p>

    <div class="row">
        <div class="col-md-5">
            <h2>Order Info</h2>

            <?= DetailView::widget([
                'model'      => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'customer',
                        'value'     => $model->customer->name,
                    ],
                    'order_reference',
                    'customer_reference',
                    'status.name',
                    'tracking',
                    'notes',
                    'uuid',
                    'requested_ship_date:date',
                    'carrier.name',
                    'service.name',
                    'origin',
                    'created_date:datetime',
                    'updated_date:datetime',
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <h2>Ship To</h2>
            <?php if ($model->address) : ?>
                <?= DetailView::widget([
                    'model'      => $model->address,
                    'attributes' => [
                        //'id',
                        'name',
                        'address1',
                        'address2',
                        'city',
                        'state.name',
                        'zip',
                        'phone',
                        'email',
                        'notes',
                        'created_date:datetime',
                        'updated_date:datetime',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>

        <div class="col-md-3">
            <h2>Items (<?= count($model->items) ?? null ?>)</h2>
            <?php
            if ($model->items) :
                $idx = 0;
                foreach ($model->items as $item) : ?>
                    <h3>#<?= ++$idx ?></h3>

                    <?= DetailView::widget([
                        'model'      => $item,
                        'attributes' => [
                            //'id',
                            'quantity',
                            'sku',
                            'name',
                        ],
                    ]);
                endforeach;
            endif; ?>
        </div>

    </div>
</div>
