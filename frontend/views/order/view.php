<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use common\models\Status;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */
/* @var $dataProviderHistory ActiveDataProvider */

$this->title = 'Order ' . $model->customer_reference;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
$cookies = Yii::$app->request->cookies;
$simple = $cookies->getValue('simple');

$statusesList = Status::getList();
$statusUrl = Url::to(['order/status-update']);

$js = <<<JS

$(".status-links").on('click', function () {
    $.ajax({
        url: "{$statusUrl}",
        data: {
            id: $(this).attr("data-id"),
            status: $(this).attr("data-status")
        },
        type: "get",
        success: function(data){
            notyf.success('Order status successfully updated!');
            $("#order-status").html(data.message);
            
        },
        error: function () {
            notyf.error('We encountered an error saving the status.');
        }
    });
});
JS;

$this->registerJs($js);
?>

<div class="order-view">

    <h1 style="display: inline"><?= Html::encode($this->title) ?></h1>
    <div style="display: inline;" id="order-status"><?= $model->status->getStatusLabel(); ?></div>
    <h4 style="margin-top: -0.5rem; color: #575555;"><?= $model->customer->name; ?> </h4>

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
            Status <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <?php foreach ($statusesList as $status_id => $name) { ?>
                <li><?= Html::a($name, null, ['class' => 'status-links', 'data-status' => $status_id, 'data-id' => $model->id]) ?></li>
            <?php } ?>
        </ul>
    </div>

    <div class="btn-group">
        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li><?= Html::a('Clone Order', ['clone', 'id' => $model->id]) ?></li>
            <li><?= Html::a('Print Packing Slip', ['packing-slip', 'id' => $model->id], ['target' => '_blank']) ?></li>
            <li><?= Html::a('Print Shipping Label', ['shipping-label', 'id' => $model->id], ['target' => '_blank']) ?></li>
            <?php
            if (!$simple) { ?>
                <li><?= Html::a('<i class="glyphicon glyphicon-eye-close"></i> Simple View', ['simple-view']); ?></li>
            <?php } else { ?>
                <li><?= Html::a('<i class="glyphicon glyphicon-eye-open"></i> Advanced View', ['simple-view']); ?></li>
            <?php } ?>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-5">
            <h2>Order Info</h2>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'order_reference',
                        'visible' => !$simple,
                    ],
                    'tracking',
                    [
                        'attribute' => 'notes',
                        'visible' => !$simple,
                    ],
                    [
                        'label' => 'Shipping',
                        'value' => function ($model) {
                            $carrier = (isset($model->carrier->name)) ? $model->carrier->name : '';
                            $service = (isset($model->service->name)) ? $model->service->name : '';
                            return $carrier . ' ' . $service;
                        },
                    ],
                    [
                        'attribute' => 'origin',
                        'visible' => !$simple,
                    ],
                    [
                        'attribute' => 'uuid',
                        'visible' => !$simple,
                    ],
                    [
                        'attribute' => 'po_number',
                        'visible' => !$simple,
                    ],
                    'created_date:datetime',
                    [
                        'attribute' => 'updated_date',
                        'format' => 'datetime',
                        'visible' => !$simple,
                    ],
                    [
                        'attribute' => 'requested_ship_date',
                        'visible' => !$simple,
                    ],
                    [
                        'attribute' => 'must_arrive_by_date',
                        'visible' => !$simple,
                    ],
                    'transit',
                    [
                        'attribute' => 'packagingNotes',
                        'visible' => !$simple,
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <h2>Ship To</h2>
            <?php if ($model->address) : ?>
                <?= DetailView::widget(['model' => $model->address,
                    'attributes' => [['label' => 'Name',
                        'value' => function ($model) {
                            $ship[] = $model->name;

                            if (isset($model->company) && !empty($model->company)) {
                                $ship[] = $model->company;
                            }

                            $ship[] = $model->address1;

                            if (isset($model->address2) && !empty($model->address2)) {
                                $ship[] = $model->address2;
                            }
                            $state = isset($model->state) ? $model->state->abbreviation : '';
                            $ship[] = $model->city . ', ' . $state . ' ' . $model->zip;
                            $ship[] = $model->country;

                            return implode('<br/>', $ship);
                        },
                        'format' => 'raw',],
                        'phone',
                        'email',
                        'notes',],]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Items (<?= count($model->items) ?? null ?>)</h2>
            <?php

            $dataProvider = new ActiveDataProvider(['query' => \frontend\models\Item::find()->where(['order_id' => $model->id]),]);
            $count = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM package_items WHERE order_id=:order_id
            ', [':order_id' => $model->id])->queryScalar();

            $dataProviderPackages = new \yii\data\SqlDataProvider(['sql' => 'SELECT * from package_items_lot_info
                            left join package_items on
                            package_items.id = package_items_lot_info.package_items_id
                            left join packages on
                            packages.id = package_items.package_id where packages.order_id = :order_id order by tracking',
                'params' => [':order_id' => $model->id],
                'totalCount' => $count,
                'sort' => ['attributes' => ['packages.tracking',
                    'name' => ['asc' => ['first_name' => SORT_ASC, 'last_name' => SORT_ASC],
                        'desc' => ['first_name' => SORT_DESC, 'last_name' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Name',],],],
                'pagination' => ['pageSize' => 20,],]);

            // get the user records in the current page
            $models = $dataProvider->getModels();
            echo GridView::widget(['dataProvider' => $dataProvider,
                'columns' => ['quantity',
                    'sku',
                    'name','type'],]); ?>

            <?php if (!$simple) { ?>
                <h2>Packages</h2>

                <?=
                    GridView::widget(['dataProvider' => $dataProviderPackages,
                        'columns' => [
                            'tracking',
                            'quantity',
                            'sku',
                            'lot_number'
                        ]])
                ?>

                <h2>Order History</h2>

                <?=
                    GridView::widget(['dataProvider' => $dataProviderHistory,
                        'columns' => [
                            [
                                'attribute' => 'user.username',
                            ],
                            'created_date:datetime',
                            [
                                'attribute' => 'notes',
                                'value' => function ($model) {
                                    return '<pre>' . HtmlPurifier::process(nl2br($model->notes)) . '</pre>';
                                },
                                'format' => 'raw',
                                'enableSorting' => false,
                            ],
                        ],
                    ])
                ?>
            <?php } ?>
        </div>

    </div>
</div>
