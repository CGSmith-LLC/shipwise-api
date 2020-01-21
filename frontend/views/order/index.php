<?php

use common\models\Status;
use frontend\models\Customer;
use yii\helpers\{Html, Url};
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\web\View;
use yii\widgets\Pjax;

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
?>
    <div class="order-index">

        <?php Pjax::begin([
            'id' => 'pjax-orders',
            'timeout' => 2000,
            //'enablePushState'    => false,
            //'enableReplaceState' => false,
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
                $markAs = array_combine(
                    array_map(function ($k) {
                        return $k;
                    }, array_keys($statuses)), $statuses
                ); ?>
                <?= Html::dropDownList('bulkAction', '',
                    [
                        '' => 'With selected: ',
                    ] + ['Change status to:' => $markAs],
                    [
                        'class' => 'form-control',
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => 'Apply bulk action to selected orders',
                    ]) ?>
            </div>
            <div class="col-lg-9">
                <div class="pull-right">
                    <?= Html::a('<i class="glyphicon glyphicon-remove-sign"></i> Clear filters', [''],
                        ['class' => 'btn btn-default btn-xs m-b-xs m-r-xs']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-refresh"></i> Refresh', false,
                        ['class' => 'btn btn-default btn-xs m-b-xs', 'id' => 'refresh-btn']) ?>
                </div>
            </div>
        </div>

        <?= GridView::widget([
            'id' => 'orders-grid-view',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterSelector' => '#' . Html::getInputId($searchModel, 'pageSize'),
            'pager' => [
                'firstPageLabel' => Yii::t('app', 'First'),
                'lastPageLabel' => Yii::t('app', 'Last'),
            ],
            'columns' => [
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
                'customer_reference',
                [
                    'attribute' => 'address',
                    'value' => 'address.name',
                ],
                'tracking',
                'created_date:datetime',
                [
                    'attribute' => 'status_id',
                    'options' => ['width' => '10%'],
                    'value' => 'status.name',
                    'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status_id',
                        $statuses,
                        ['class' => 'form-control', 'prompt' => Yii::t('app', 'All')]
                    ),
                ],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>

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
                    alert('No shipments selected. Click on checkboxes to select one or multiple shipments.');
                    return false;
                }
                bulk(ids, action, actionText);
            });

            $('#modalBulk').on('hidden.bs.modal', function () {
                reloadGrid();
            })

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
         * Reload shipment grid
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
         * Perform bulk action on selected shipments
         *
         * @param ids Shipment IDs
         * @param action The code of the action to perform
         * @param actionText User-friendly text of the action
         */
        function bulk(ids, action, actionText) {

            var popup = $('#modalBulk'),
                btnConfirm = popup.find('button.confirm');

            btnConfirm.attr('disabled', false).show();
            popup.modal('show').find('.modal-body').html(
                'Please confirm you want to perform bulk action <strong>' + actionText +
                '</strong> on ' + ids.length + ' shipments.'
            );

            btnConfirm.off().on('click', function () {
                btnConfirm.attr('disabled', true);
                popup.find('.modal-body').load('<?= Url::to(['bulk']) ?>', {
                    "BulkAction": {
                        "action": action,
                        "orderIDs": ids
                    }
                }, function () {
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