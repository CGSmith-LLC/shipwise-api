<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use frontend\assets\DatePickerAsset;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\OrderForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $statuses array List of order statuses */
/* @var $carriers array List of carriers */
/* @var $services array List of carrier services */
/* @var $states array List of states */

DatePickerAsset::register($this);

?>

    <div class="order-form">

        <?php $form = ActiveForm::begin([
            'id'          => 'form-order',
            'layout'      => 'horizontal',
            'fieldConfig' => [
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
        <?= Html::hiddenInput('order_id', $model->order->id ?? null) ?>
        <?= $model->errorSummary($form); ?>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Order Info</h3>
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model->order, 'order_reference')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->order, 'customer_reference')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model->order, 'requested_ship_date', [
                            'inputTemplate' =>
                                '<div class="input-group date"><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>{input}</div>',
                        ]) ?>

                        <?= $form->field($model->order, 'tracking')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model->order, 'status_id')
                                 ->dropdownList($statuses, [
                                     'disabled' => $model->order->isNewRecord,
                                     'prompt'   => ' -- Unknown --',
                                 ]) ?>

                        <?= $form->field($model->order, 'notes')->textarea(['rows' => 3, 'maxlength' => true]) ?>

                        <?= $form->field($model->order, 'carrier_id')
                                 ->dropdownList($carriers, ['prompt' => ' -- Unknown --']) ?>

                        <?= $form->field($model->order, 'service_id')->dropdownList($services, [
                            'prompt' => ' -- Unknown --',
                        ]) ?>

                    </div>
                </div>

                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Items</h3>
                    </div>
                    <div class="panel-body">

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
                        <?= $form->field($model->address, 'address1')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'address2')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'city')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model->address, 'state_id')
                                 ->dropdownList($states, ['prompt' => ' Please select']) ?>

                        <?= $form->field($model->address, 'zip')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'phone')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'email')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model->address, 'notes')->textarea(['rows' => 3, 'maxlength' => true]) ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-lg btn-success']) ?>
                <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-lg btn-default']) ?>
            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>

<?php $this->registerJs("
    initForm();
    ",
    View::POS_READY,
    'order-handler-pos-ready'
);

ob_start(); // output buffer the javascript to register later ?>
    <script>

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
                format             : 'yyyy-mm-dd',
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

            $.get(url, function ( response ) {
                var items = JSON.parse(response);
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

            if (items && Object.keys(items).length > 0) {
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

    </script>
<?php $this->registerJs(
    str_replace(['<script>', '</script>'], '', ob_get_clean()),
    View::POS_END,
    'order-handler-pos-end'
); ?>