<?php

use yii\helpers\Html;
use yii\web\YiiAsset;

/* @var $this yii\web\View */
/* @var $model frontend\models\Order */
/* @var $carriers array */
/* @var $services array */
/* @var $states array */
/* @var $customers array */

$this->title = 'Import orders ';

$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="order-import">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-3">
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
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-success">
                <div class="panel-body bg-success">
                    <div class="jumbotron">
                        <h2>Step One</h2>
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
            <div class="panel panel-success">
                <div class="panel-body bg-success">
                    <div class="jumbotron">
                        <h2>Step Two</h2>
                        <p>
                            <a class="btn btn-success btn-lg" href="#" role="button"><i class="fa fa-upload"></i> Import</a>
                        </p>
                    </div>
                </div>
            </div>
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
                            <p><strong>Carrier codes</strong></p>
                            <?php
                            foreach ($carriers as $code) {
                                echo $code . '<br />';
                            } ?>
                            <hr class="visible-sm" />
                        </div>
                        <div class="col-md-3">
                            <p><strong>Service codes</strong></p>
                            <?php
                            foreach ($services as $code) {
                                if (!in_array($code, ['to-be-defined', 'tbd'])) {
                                    echo $code . '<br />';
                                }
                            } ?>
                            <hr class="visible-sm" />
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
