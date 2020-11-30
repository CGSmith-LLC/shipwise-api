<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */
/* @var $carriers array */
/* @var $services array */
/* @var $states array */
/* @var $customers array */

$title = 'Import orders';

$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
YiiAsset::register($this);

if ($model->customer) {
    $title .= ' (' . ($customers[$model->customer] ?? '') . ')';
}
?>
    <div class="order-import">

        <h1><?= Html::encode($title) ?></h1>

        <div class="row">
            <div class="col-md-3">
                <?= Html::beginForm(['import'], 'get', ['id' => 'form-customer']) ?>
                <div class="form-group">
                    <label><?= $model->getAttributeLabel('customer') ?></label>
                    <?= Html::activeDropDownList(
                        $model,
                        'customer',
                        $customers,
                        [
                            'prompt' => ' Please select',
                            'class'  => 'form-control',
                        ]
                    ) ?>
                </div>
                <?= Html::endForm() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-body bg-success">
                        <div class="jumbotron">
                            <h2 style="margin-bottom: 50px">Step One</h2>
                            <?= Html::beginForm(['download-csv-template'], 'post', ['id' => 'form-template']) ?>
                            <p>
                                <?= Html::submitButton(
                                    '<i class="fa fa-download"></i> Get CSV template',
                                    ['class' => 'btn btn-lg btn-success']
                                ) ?>
                            </p>
                            <?= Html::endForm() ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <?php
                $form = ActiveForm::begin(
                    [
                        'id'      => 'form-import',
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]
                ) ?>
                <?= Html::errorSummary($model, ['class' => 'alert alert-danger']) ?>
                <div class="panel panel-success">
                    <div class="panel-body bg-success">
                        <div class="jumbotron">
                            <h2 class="top">Step Two</h2>
                            <?= $form->field($model, 'customer')->hiddenInput()->label(false) ?>
                            <?= $form->field($model, 'file')->fileInput(['class' => 'm-auto'])->label(false) ?>
                            <p>
                                <?= Html::submitButton(
                                    '<i class="fa fa-upload"></i> Import',
                                    ['class' => 'btn btn-lg btn-success']
                                ) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
                ActiveForm::end() ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Reference data</h3>
                    </div>
                    <div class="panel-body text-muted">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>Carrier service codes</strong></p>
                                <?php
                                foreach ($services as $code) {
                                    if (!in_array($code, ['to-be-defined', 'tbd'])) {
                                        echo "<code>$code</code><br />";
                                    }
                                } ?>
                                <hr class="visible-xs visible-sm" />
                            </div>
                            <div class="col-md-3">
                                <p><strong>Datetime format</strong></p>
                                YYYY-MM-DD<br />
                                eg. <code><?= (new \DateTime('now'))->format('Y-m-d') ?></code>
                                <hr class="visible-xs visible-sm" />
                            </div>
                            <div class="col-md-3">
                                <p><strong>Country</strong></p>
                                eg. <code>US</code> for USA, <code>CA</code> for Canada, etc.
                                <hr class="visible-xs visible-sm" />
                            </div>
                            <div class="col-md-3">
                                <p><strong>State / Province</strong></p>
                                eg. <code>IL</code> for Illinois, <code>ON</code> for Ontario, etc.
                                <hr class="visible-xs visible-sm" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$this->registerJs(
    "
$('#orderimport-customer').change(function() { 
    $('#form-customer').submit(); 
});
",
    View::POS_READY,
    'order-import-pos-ready'
);
