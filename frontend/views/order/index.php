<?php

use common\models\Status;
use frontend\models\{Customer, BulkAction};
use yii\helpers\{Html, Json, Url};
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;
use frontend\models\ColumnManage;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $statuses array List of order statuses */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
/**
 * - Get a dropdown list from the associated customers
 * - OR - get a dropdown list of all customers if admin
 */
if ((!Yii::$app->user->identity->getIsAdmin())) {
    $customerDropdownList = Yii::$app->user->identity->getCustomerList();
} else {
    $customerDropdownList = Customer::getList();
}

$customColumns = ColumnManage::getColumnManageOfUser();
$generateColumns = ColumnManage::generateColumns();
?>
    <div class="order-index">

        <?php Pjax::begin([
            'id' => 'pjax-orders',
            'timeout' => 2000,
        ]) ?>
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Create Order', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <div class="row m-b-xs">
            <div class="col-lg-1 col-md-2 col-sm-2 col-xs-3">
                <?= Html::dropDownList('OrderSearch[pageSize]', $searchModel->pageSize,
                    $searchModel->pageSizeOptions,
                    [
                        'id' => 'ordersearch-pagesize',
                        'class' => 'form-control',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'right',
                        'title' => '# of entries to show per page',
                    ]) ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4">
                <?php
                $statuses = Status::getList();
                $changeStatus = array_combine(
                    array_map(function ($k) {
                        return BulkAction::ACTION_CHANGE_STATUS . "_$k"; // <action>_<value>
                    }, array_keys($statuses)), $statuses
                ); ?>
                <?= Html::dropDownList('bulkAction', '',
                    [
                        '' => 'With selected: ',
                    ] +
                    [
                        'Batch:' => BulkAction::getBatchActionsList(),
                    ] +
                    [
                        'Print:' => BulkAction::getPrintActionsList(),
                    ] +
                    [
                        'Change Carrier/Service:' => [BulkAction::ACTION_UPDATE_CARRIER => 'Change Carrier/Service'],
                    ]
                    + ['Change status to:' => $changeStatus],
                    [
                        'class' => 'form-control',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'Apply bulk action to selected orders',
                    ]) ?>
            </div>

            <div class="col-lg-9">
                <div class="pull-right">
                    <?= Html::button('<i class="glyphicon glyphicon-list"></i> Column Manager',
                        ['class' => 'btn btn-default btn-xs m-b-xs', 'data-toggle' => 'modal', 'data-target' => '#columnModal']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-sign"></i> Clear filters',
                        ['/order?' . urlencode('OrderSearch[clearfilters]') . '=1'],
                        ['class' => 'btn btn-default btn-xs m-b-xs m-r-xs']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', false,
                        ['class' => 'btn btn-default btn-xs m-b-xs', 'id' => 'refresh-btn']) ?>
                </div>
            </div>
        </div>

        <?php
        $columns = array(
            [
                'class' => 'yii\grid\CheckboxColumn',
            ],
            [
                'attribute' => 'customer.name',
                // Visible if admin or has a count higher than 0 for associated users
                'visible' => ((count($customerDropdownList) > 1) || Yii::$app->user->identity->getIsAdmin()),
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'customer_id',
                    $customerDropdownList,
                    ['class' => 'form-control', 'prompt' => Yii::t('app', 'All Customers')]
                ),
            ],
        );

        foreach($generateColumns as $value) {
            if ($value == 'carrier_id') {
                $userColumns[] = [
                    'attribute' => 'carrier_id',
                    'options' => ['width' => '10%'],
                    'value' => 'carrier.name',
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'carrier_id',
                        $carriers,
                        ['class' => 'form-control', 'prompt' => Yii::t('app', 'All')]
                    ),
                ];
            } elseif ($value == 'address') {
                $userColumns[] = [
                    'attribute' => 'address',
                    'value' => 'address.name',
                ];
            } elseif ($value == 'notes') {
                $userColumns[] = [
                    'attribute' => 'notes',
                    'value' => function ($model) {
                        return yii\helpers\StringHelper::truncate($model->notes, 40);
                    }
                ];
            } elseif ($value == 'status_id') {
                $userColumns[] = [
                    'attribute' => 'status_id',
                    'options' => ['width' => '10%'],
                    'value' => 'status.name',
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status_id',
                        $statuses,
                        ['class' => 'form-control', 'prompt' => Yii::t('app', 'All')]
                    ),
                ];
            } elseif (in_array($value, ['created_date', 'requested_ship_date', 'updated_date'])) {
                $userColumns[] = $value . ':datetime';
            } elseif ($value == 'service_id') {
                $userColumns[] = 'service.name';
            } else {
                $userColumns[] = $value;
            }
        }
        $userColumns[] = [
            'class' => 'yii\grid\ActionColumn',
            'options' => ['width' => '15%'],
        ];

        $columns = array_merge($columns, $userColumns);
        ?>
        <?= GridView::widget([
            'id' => 'orders-grid-view',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterSelector' => '#' . Html::getInputId($searchModel, 'pageSize'),
            'pager' => [
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
            ],
            'columns' => $columns,
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
<?= $this->render('partial/_column_modal', array(
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'customColumns' => $customColumns,
)) ?>
<?php Modal::begin([
    'id' => 'modalBulk',
    'clientOptions' => ['backdrop' => 'static', 'keyboard' => false],
    'header' => '<h4>Bulk Action</h4>',
    'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="confirm btn btn-primary">Confirm</button>',
]);
Modal::end();

ob_start(); // output buffer the javascript to register later ?>
    <script>

        initListeners();

        $(document).on('ready pjax:success', function () {
            initListeners();
        });

        $('.order-index').tooltip({
            selector: '[data-toggle="tooltip"]',
            container: 'body',
            trigger: 'hover'
        });

    </script>
<?php $this->registerJs(
    str_replace(['<script>', '</script>'], '', ob_get_clean()),
    View::POS_READY,
    'manage-orders-handler-pos-ready'
);

ob_start(); // output buffer the javascript to register later ?>
    <script>

        /**
         * Event listeners
         */
        function initListeners() {

            $('#refresh-btn').click(function () {
                btn = $(this);
                simpleLoad(btn, true);
                simpleLoad(btn, false);
                reloadGrid();
            });

            $("[name='bulkAction']").change(function () {
                var action = $(this).val(),
                    actionText = $(this).find('option:selected').text(),
                    ids = $('#orders-grid-view').yiiGridView('getSelectedRows');

                $(this).val(''); // reset

                if (action === '') {
                    return false;
                }
                if (ids && ids.length === 0) {
                    alert('No orders selected. Click on checkboxes to select one or multiple orders.');
                    return false;
                }
                bulk(ids, action, actionText);
            });

        }

        /**
         * Simple effect for button loading
         *
         * @param btn
         * @param state
         */
        function simpleLoad(btn, state) {
            if (state) {
                btn.children().addClass('fa-spin');
                btn.contents().last().replaceWith(' Loading');
            } else {
                setTimeout(function () {
                    btn.children().removeClass('fa-spin');
                    btn.contents().last().replaceWith(' Refresh');
                }, 1000);
            }
        }

        /**
         * Reload orders grid
         */
        function reloadGrid() {
            $('#orders-grid-view').yiiGridView('applyFilter');
        }

        /**
         * Hide all tooltips
         */
        function hideTooltips() {
            $('.tooltip').hide();
        }

        /**
         * Perform bulk action on selected orders
         *
         * @param ids Order IDs
         * @param action The code of the action to perform
         * @param actionText User-friendly text of the action
         */
        function bulk(ids, action, actionText) {

            var popup = $('#modalBulk'),
                btnConfirm = popup.find('button.confirm'),
                printingActions = <?= Json::encode(array_keys(BulkAction::getPrintActionsList())); ?>,
                batchNames = <?= Json::encode(\common\models\base\BaseBatch::getList('id', 'name', 'created_date', SORT_DESC, ['customer_id' => Yii::$app->user->identity->customer_id]), JSON_OBJECT_AS_ARRAY);?>;

            btnConfirm.attr('disabled', false).show();

            popup.modal('show').find('.modal-body').html(
                '<div class="alert alert-warning">' +
                'Please confirm you want to perform bulk action <strong>' + actionText +
                '</strong> on ' + ids.length + ' orders.' +
                '</div>'
            );

            // additional input options based on action type
            var container = popup.find('.modal-body');
            if (jQuery.inArray(action, printingActions) !== -1) {
                $('<input />', {type: 'checkbox', id: 'bulkaction-print_as_pdf', checked: true}).appendTo(container);
                $('<label />', {'for': 'bulkaction-print_as_pdf', text: ' Print as PDF'}).appendTo(container);
            }

            if (action === '<?=BulkAction::ACTION_BATCH_CREATE;?>') {
                $('<input />', {type: 'text', id: 'bulkaction-batch_name'}).appendTo(container);
                $('<label />', {'for': 'bulkaction-batch_name', text: ' Batch Name'}).appendTo(container);
            }

            if (action === '<?=BulkAction::ACTION_BATCH_ADD;?>') {
                $('<select />', {type: 'text', id: 'bulkaction-batch_id'}).appendTo(container);
                $.each(batchNames, function (index, value) {
                    $('#bulkaction-batch_id').append($('<option/>', {
                        value: index,
                        text: value
                    }));
                });
                $('<label />', {'for': 'bulkaction-batch_id', text: ' Batch Name'}).appendTo(container);
            }

            if (action === '<?=BulkAction::ACTION_UPDATE_CARRIER;?>') {
                $('<div>').load('/order/carrier-modal').appendTo(container);
            }

            btnConfirm.off().on('click', function () {
                btnConfirm.attr('disabled', true);

                $.ajax({
                    url: '<?= Url::to(['bulk']) ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {
                        "BulkAction": {
                            "action": action,
                            "orderIDs": ids,
                            "options": {
                                "print_as_pdf": $('#bulkaction-print_as_pdf').is(':checked') ? 1 : 0,
                                "batch_name": $('#bulkaction-batch_name').val() ? $('#bulkaction-batch_name').val() : '',
                                "batch_id": $('#bulkaction-batch_id').val() ? $('#bulkaction-batch_id').val() : '',
                                "carrier_id": $('#carrier_id').val(),
                                "service_id": $('#service_id').val(),
                            }
                        }
                    }
                })
                    .done(function (response) {
                        if (response.success) {
                            reloadGrid();
                            popup.find('.modal-body').html('<div class="alert alert-success">' + response.message + '</div>');
                            if (response.link) {
                                popup.modal('toggle');
                                var win = window.open(response.link, '_blank');
                                win.focus();
                            }
                        } else {
                            popup.find('.modal-body').html('<div class="alert alert-danger">' + response.errors + '</div>');
                        }

                    })
                    .fail(function (jqXHR, textStatus, error) {
                        popup.find('.modal-body').html(error).append(jqXHR.responseText || '');
                    })
                    .always(function () {
                        btnConfirm.hide();
                    });

            });
        }

    </script>
<?php $this->registerJs(
    str_replace(['<script>', '</script>'], '', ob_get_clean()),
    View::POS_END,
    'manage-orders-handler-pos-end'
);
