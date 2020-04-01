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

    <p>
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
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Items (<?= count($model->items) ?? null ?>)</h2>
            <?php
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
                        /*[
                        'attribute' => 'tracking',
                        'value' => function ($model) use ($dataProviderPackages) {
                            //if ($model->tracking ) {
                                Yii::debug($dataProviderPackages);
                            //}
                        }
                    ]*/
                    'quantity',
                    'sku',
                    'lot_number',
                ],
            ]);

            ?>
        </div>

    </div>
</div>
