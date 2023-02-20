<?php

use common\models\Package;
use common\models\PackageItemLotInfo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;
use common\models\Status;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */
/* @var $reopenModel frontend\models\forms\ReopenOrderEditForm */
/* @var $historyDataProvider yii\data\ActiveDataProvider */
/* @var $packagesDataProvider yii\data\SqlDataProvider */
/* @var $itemsDataProvider yii\data\ActiveDataProvider */
/* @var $simple boolean view mode */
/* @var $status int hold status */
/* @var $statusesList array  status list */

$this->title = 'Order ' . $model->customer_reference;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

$statusUrl = Url::to(['order/status-update']);

\frontend\assets\ToggleAsset::register($this);

$js = <<<JS

$(".status-links").on('click', function () {
    var statusData =  {
            id: $(this).attr("data-id"),
            status: $(this).attr("data-status")
    };
    
    $.ajax({
        url: "{$statusUrl}",
        data: statusData,
        type: "get",
        success: function(data){
            notyf.success('Order status successfully updated!');
            $("#order-status").html(data.message);
        
            if (statusData.status == {$status}) {
                $('#reopen-dialog').show();
            } else {
                $('#bulkeditform-reopen_enable').prop('checked', false).change()
                $('#reopen-dialog').hide();
            }
        },
        error: function () {
            notyf.error('We encountered an error saving the status.');
        }
    });
});
JS;

$this->registerJs($js);

$js = <<<JS

$("#form-reopen-edit").on('change', function () {
    $('#reopen-form-submit-btn').removeAttr('disabled').removeClass('disabled');
});

$("#reopenordereditform-reopen_enable").on('change', function() {
    if ($(this).prop('checked')) {
        $('#reopen-date').show();
    }else {
        $('#reopen-date').hide();
    }
})
JS;

$this->registerJs($js);

\frontend\assets\DatePickerAsset::register($this);
$this->registerJs('
    // Datepicker
    $(\'.date\').flatpickr({
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y H:i",    
    minDate: "today",
})');
?>

<div class="order-view">

    <h1 style="display: inline"><?= Html::encode($this->title) ?></h1>
    <div style="display: inline;" id="order-status"><?= $model->status->getStatusLabel() ?></div>

    <h4 style="margin-top: -0.5rem; color: #575555;"><?= Html::encode($model->customer->name) ?> </h4>


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
        <div class="col-md-6">

    <?php $form = ActiveForm::begin([
        'id' => 'form-reopen-edit'
    ]); ?>

    <div id="reopen-dialog" style="<?= ($model->status_id == $status) ?  '': 'display: none'; ?>; padding-top: 0.5em">
        <div class="panel panel-default">
            <div class="panel-body">
        <label>Do you want to automatically change the status to Open at a specific time?</label>
        <?= $form->field($reopenModel, 'reopen_enable')->checkbox([
            'readonly' => true,
            'data-toggle' => 'toggle',
            'data-on' => 'Yes',
            'data-off' => 'No',
            'label' => false,
        ]); ?>

        <div id="reopen-date" style="<?= (!$reopenModel->reopen_enable) ? 'display: none' : ''; ?>">
            <?= $form->field($reopenModel, 'open_date', [
                'inputOptions' => ['autocomplete' => 'off']
            ])->textInput([
                'readonly' => true,
                'class' => 'date',
                'value' => (isset($reopenModel->open_date)) ? $reopenModel->open_date : '',
            ])->label('Date and time to automatically change orders to Open'); ?>
        </div>
        <?= Html::submitButton( 'Save Changes', ['id'=>'reopen-form-submit-btn', 'class' => 'btn  btn-success disabled']); ?>
    </div>


    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
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

            // get the user records in the current page
            echo \yii\grid\GridView::widget(['dataProvider' => $itemsDataProvider,
                'columns' => ['quantity',
                    'sku',
                    'name','type'],]); ?>
            <?php
            if (!$simple) {
                ?>
                <h2>Packages</h2>
                <?php
                echo \yii\grid\GridView::widget(['dataProvider' => $packagesDataProvider,
                    'columns' => ['tracking',
                        'quantity',
                        'sku',
                        'lot_number',],]);
                ?>
                <h2>Order History</h2>
                <?php
                echo \yii\grid\GridView::widget(['dataProvider' => $historyDataProvider,
                    'columns' => [
                        'created_date:datetime',
                        [
                            'attribute' => 'comment',
                            'value' => function ($model) {
                                return '<p style="text-overflow:ellipsis;overflow:hidden;white-space:nowrap;width: 900px;">'.nl2br(Html::encode($model->comment)).'</p>';

                            },
                            'format' => 'raw',
                        ],
                    ],
                ]);
            }
            ?>
        </div>

    </div>
</div>
