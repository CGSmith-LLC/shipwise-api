<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use frontend\assets\DatePickerAsset;
use frontend\models\Item;

/* @var $this yii\web\View */
/* @var $model common\models\forms\OrderForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $customers array List of customers */
/* @var $statuses array List of order statuses */
/* @var $carriers array List of carriers */
/* @var $services array List of carrier services */
/* @var $states array List of states */
/* @var $countries array list of states */

DatePickerAsset::register($this);

$item = new Item();
$item->loadDefaultValues();

?>

    <div class="order-form">

        <?php $form = ActiveForm::begin([
            'id'                     => 'form-order',
            'layout'                 => 'horizontal',
            'enableClientValidation' => false,
            'fieldConfig'            => [
                'template'             => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                'horizontalCssClasses' => [
                    'label'   => 'col-sm-4',
                    'offset'  => 'col-sm-offset-4',
                    'wrapper' => 'col-sm-8',
                    'error'   => '',
                    'hint'    => '',
                ],
            ],
        ]); ?>
        <?= $model->errorSummary($form); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Order Info</h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model->order, 'status_id')
                            ->dropdownList($statuses, [
                                'disabled' => $model->order->isNewRecord,
                                'prompt'   => ' -- Unknown --',
                            ]) ?>

                        <?= $form->field($model->order, 'customer_id')
                                 ->dropdownList($customers, ['prompt' => ' Please select']) ?>


                        <?php
                        // i think i only want this for admins right now... needs more thought
                        if (Yii::$app->user->identity->getIsAdmin()) {
                            echo $form->field($model->order, 'warehouse_id')
                                ->dropdownList($warehouses, ['prompt' => ' Please select']);
                        }
                        ?>

                        <?= $form->field($model->order, 'tracking')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model->order, 'customer_reference')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model->order, 'po_number')->textInput(['maxlength' => true]) ?>

                        <?php
                        if (Yii::$app->user->identity->getIsAdmin()) {
                           echo $form->field($model->order, 'order_reference')->textInput(['maxlength' => true]);
                        }
                        ?>

                        <?= $form->field($model->order, 'carrier_id')
                                 ->dropdownList($carriers, ['prompt' => ' -- Unknown --']) ?>

                        <?= $form->field($model->order, 'service_id')
                            ->dropdownList($services, ['prompt' => ' -- Unknown --',]) ?>

                        <?= $form->field($model->order, 'requested_ship_date', [
                            'inputTemplate' =>
                                '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
                        ])->textInput([
                            'value' => (isset($model->order->requested_ship_date)) ? Yii::$app->formatter->asDate($model->order->requested_ship_date) : '',
                        ]) ?>

                        <?= $form->field($model->order, 'must_arrive_by_date', [
                            'inputTemplate' =>
                                '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
                        ])->textInput([
                            'value' => (isset($model->order->must_arrive_by_date)) ? Yii::$app->formatter->asDate($model->order->must_arrive_by_date) : '',
                        ]) ?>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Ship To</h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model->address, 'name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'company')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'address1')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'address2')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'city')->textInput(['maxlength' => true]) ?>


                        <?= $form->field($model->address, 'country')
                            ->dropdownList($countries, ['default' => 'US']) ?>

                        <?= $form->field($model->address, 'state_id')
                                 ->dropdownList($states, ['prompt' => ' Please select']) ?>

                        <?= $form->field($model->address, 'zip')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'phone')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'email')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'notes')->textInput(['maxlength' => true])->label('Notes') ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Items</h3>
                    </div>
                    <div class="panel-body">

                        <div id="items">
                            <?= Html::a('<i class="glyphicon glyphicon-plus"></i> add another item', 'javascript:void(0);', [
                                'id'    => 'btn-add-item',
                                'class' => 'btn btn-warning btn-sm',
                            ]) ?>
                            <?php
                            // existing item fields
                            foreach ($model->items as $itemKey => $_item) : ?>
                                <div class="row item item-<?= $itemKey ?>">
                                    <?= $this->render('partial/_item-row', [
                                        'key'     => $_item->isNewRecord
                                            ? (strpos($itemKey, 'new') !== false ? $itemKey : 'new' . $itemKey)
                                            : $_item->id,
                                        'form'    => $form,
                                        'item'    => $_item,
                                        'counter' => $itemKey,
                                    ]); ?>
                                </div>
                            <?php endforeach; ?>

                            <div id="new-item-block" class="row hidden">
                                <?= $this->render('partial/_item-row', [
                                    'key'     => '__id__',
                                    'form'    => $form,
                                    'item'    => $item,
                                    'counter' => 0,
                                ]);
                                ?>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-lg btn-success']) ?>
                    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-lg btn-default']) ?>
                </div>
            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>

<?php
ob_start(); // output buffer the javascript to register later ?>
    <script>

        initForm();

        <?php
        // Trigger btn-add-item button to add first item if order is a new record.
        if (!Yii::$app->request->isPost && $model->order->isNewRecord) {
            echo "$('#btn-add-item').click();";
        }
        ?>

    </script>
<?php $this->registerJs(
    str_replace(['<script>', '</script>'], '', ob_get_clean()),
    View::POS_READY,
    'order-handler-pos-ready'
);

ob_start(); // output buffer the javascript to register later ?>
    <script>

        // add item button
        var itemKey = <?= isset($itemKey) ? str_replace('new', '', $itemKey) : 0 ?>;

        /**
         * Initialize form
         */
        function initForm() {

            // Datepicker
            $('.date').datepicker({
                todayBtn           : 'linked',
                keyboardNavigation : false,
                forceParse         : false,
                autoclose          : true,
                format             : 'mm/dd/yyyy',
                todayHighlight     : true,
            });

            initListeners();
        }

        /**
         * Event listeners
         */
        function initListeners() {

            $('#order-carrier_id').off('change').on('change', function () {
                getCarrierServices();
            });
            $('#address-country').off('change').on('change', function () {
                getCountryStates();
            });
            $("#btn-add-item").off('click').on("click", addItem);

            // remove item button
            $(document).on('click', '.btn-remove-item', function () {
                $(this).closest('.row').remove();
            });
        }

        /**
         * Get Carrier Services
         */
        function getCarrierServices() {

            var carrierId = $('#order-carrier_id').val() || null;

            if (!carrierId) {
                return false;
            }

            var url           = '<?= Url::to(['carrier-services']) . '?carrierId=' ?>' + carrierId,
                dropdown      = $('#order-service_id'),
                previousValue = dropdown.val() || '<?= $model->order->service_id ?>';

            dropdown.attr('disabled', 'disabled');

            $.get({url: url, dataType: 'json'}, function ( items ) {
                populateDropdown(dropdown, items, true);
                if (previousValue && (0 != dropdown.find('option[value=' + previousValue + ']').length)) {
                    dropdown.val(previousValue);
                } else {
                    dropdown.find('option:first-child').attr('selected', 'selected');
                }
                dropdown.attr('disabled', false);
                initForm();
            });
        }
        function getCountryStates() {

            var country = $('#address-country').val() || null;

            if (!country) {
                return false;
            }

            var url           = '<?= Url::to(['country-states']) . '?country=' ?>' + country,
                dropdown      = $('#address-state_id'),
                previousValue = dropdown.val() || '<?= $model->address->country ?>';

            dropdown.attr('disabled', 'disabled');

            $.get({url: url, dataType: 'json'}, function ( items ) {
                populateDropdown(dropdown, items, true);
                if (previousValue && (0 != dropdown.find('option[value=' + previousValue + ']').length)) {
                    dropdown.val(previousValue);
                } else {
                    dropdown.find('option:first-child').attr('selected', 'selected');
                }
                dropdown.attr('disabled', false);
                initForm();
            });
        }
        /**
         * Populate drop-down box
         *
         * @param elem
         * @param items
         * @param defaultOption
         */
        function populateDropdown( elem, items, defaultOption = false ) {

            if (items) {
                elem.empty();
                if (defaultOption) {
                    elem.append($('<option/>', { value : '', text : ' -- Unknown --' }));
                }
                for (var key in items) {
                    elem.append($('<option/>', {
                        value : key,
                        text  : items[ key ]
                    }));
                }
            }
        }

        /**
         * Add new item
         */
        function addItem() {

            itemKey += 1;
            $("#items").append(
                '<div class="row item item-' + itemKey + '">'
                + $('#new-item-block').html().replace(/__id__/g, 'new' + itemKey)
                + '</div>'
            );
            // disable remove button on first item
            var row = $('.item-0').length ? $('.item-0') : $('.item-1');
            row.find('.btn-remove-item').addClass('hidden');
            if (itemKey !== 1) {
                // hide label titles
                $('.item-' + itemKey).find('label').not('.fake').html('');
                // enable remove btn
                $('.item-' + itemKey).find('.btn-remove-item').removeClass('hidden');
            }
            initListeners();
        }

    </script>
<?php $this->registerJs(
    str_replace(['<script>', '</script>'], '', ob_get_clean()),
    View::POS_END,
    'order-handler-pos-end'
); ?>
