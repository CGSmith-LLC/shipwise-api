<?php

use common\models\Package;
use common\models\PackageItemLotInfo;
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

    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>

    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><?= Html::a('Clone Order', ['clone', 'id' => $model->id]) ?></li>
            <li><?= Html::a('Print Packing Slip', ['packing-slip', 'id' => $model->id], ['target' => '_blank']) ?></li>
            <li><?= Html::a('Print Shipping Label', ['shipping-label', 'id' => $model->id], ['target' => '_blank']) ?></li>
        </ul>
    </div>


    <div class="row">
        <div class="col-md-5">
            <h2>Order Info</h2>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'customer',
                        'value' => $model->customer->name,
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
                    'po_number',
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <h2>Ship To</h2>
            <?php if ($model->address) : ?>
                <?= DetailView::widget([
                    'model' => $model->address,
                    'attributes' => [
                        //'id',
                        'name',
                        'company',
                        'address1',
                        'address2',
                        'city',
                        'state.name',
                        'country',
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
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Items (<?= count($model->items) ?? null ?>)</h2>
            <?php
            $dataproviderHistory = new \yii\data\ActiveDataProvider([
                'query' => \common\models\OrderHistory::find()->where(['order_id' => $model->id])
            ]);

            $dataProvider = new \yii\data\ActiveDataProvider([
                'query' => \frontend\models\Item::find()->where(['order_id' => $model->id]),
            ]);
            $count = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM package_items WHERE order_id=:order_id
            ', [':order_id' => $model->id])->queryScalar();

            $dataProviderPackages = new \yii\data\SqlDataProvider([
                'sql' => 'SELECT * from package_items_lot_info
                            left join package_items on
                            package_items.id = package_items_lot_info.package_items_id
                            left join packages on
                            packages.id = package_items.package_id where packages.order_id = :order_id order by tracking',
                'params' => [':order_id' => $model->id],
                'totalCount' => $count,
                'sort' => [
                    'attributes' => [
                        'packages.tracking',
                        'name' => [
                            'asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                            'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                            'default' => SORT_DESC,
                            'label' => 'Name',
                        ],
                    ],
                ],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);

            // get the user records in the current page
            $models = $dataProvider->getModels();
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'header' => 'Line #',
                    ],
                    'quantity',
                    'sku',
                    'name',
                ],
            ]); ?>
            <h2>Packages</h2>
            <?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProviderPackages,
                'columns' => [
                    'tracking',
                    'quantity',
                    'sku',
                    'lot_number',
                ],
            ]);

            ?>
            <h2>Order History</h2>
            <?php
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataproviderHistory,
                'columns' => [
                    'created_date:datetime',
                    [
                        'attribute' => 'comment',
                        'value' => function ($model) {
                            return nl2br($model->comment);
                        },
                        'format' => 'raw',
                    ]
                ],
            ]);

            ?>
        </div>

    </div>
</div>
