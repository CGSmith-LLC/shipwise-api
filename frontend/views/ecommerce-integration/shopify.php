<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\forms\platforms\ConnectShopifyStoreForm;
use common\services\platforms\ShopifyService;
use common\models\Customer;

/* @var $this View */
/* @var $model ConnectShopifyStoreForm */

$title = 'Shopify Integration';
$this->title = $title . ' - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'E-commerce Integrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;

$customersList = ArrayHelper::map(
    Customer::find()
        ->orderBy(['name' => SORT_ASC])
        ->all(), 'id','name');

$orderStatuses = '<span><i class="glyphicon glyphicon-info-sign" title="Choose specific statuses for orders we must work with. Leave it empty if you want to have orders with ANY statuses."></i> Order Statuses</span>';
$financialStatusesLabel = '<span><i class="glyphicon glyphicon-info-sign" title="Choose specific financial statuses for orders we must work with. Leave it empty if you want to have orders with ANY statuses."></i> Financial Statuses</span>';
$fulfillmentStatusesLabel = '<span><i class="glyphicon glyphicon-info-sign" title="Choose specific fulfillment statuses for orders we must work with. Leave it empty if you want to have orders with ANY statuses."></i> Fulfillment Statuses</span>';
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
    </h1>

    <div class="well well-sm">
        <div class="mb-2">
            Please input your Shopify shop name and its URL without http(s).
            Example: <span class="text-muted">myshop.myshopify.com</span>.
        </div>
        <?php $form = ActiveForm::begin(); ?>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form
                        ->field($model, 'name')
                        ->textInput([
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('name') . '...',
                            'required' => true,
                            'autofocus' => true,
                            'maxlength' => true
                        ]) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form
                        ->field($model, 'url')
                        ->textInput([
                            'class' => 'form-control',
                            'placeholder' => $model->getAttributeLabel('url') . '...',
                            'required' => true,
                            'maxlength' => true
                        ]) ?>
                </div>

                <div class="col-sm-12">
                    <?= $form
                        ->field($model, 'order_statuses')
                        ->checkboxList(ShopifyService::$orderStatuses, [
                            'itemOptions' => [
                                'labelOptions' => [
                                    'class' => 'font-weight-normal mr-1',
                                ],
                            ],
                            'placeholder' => $model->getAttributeLabel('order_statuses') . '...',
                        ])
                        ->label($orderStatuses) ?>
                </div>

                <div class="col-sm-12">
                    <?= $form
                        ->field($model, 'financial_statuses')
                        ->checkboxList(ShopifyService::$financialStatuses, [
                            'itemOptions' => [
                                'labelOptions' => [
                                    'class' => 'font-weight-normal mr-1',
                                ],
                            ],
                            'placeholder' => $model->getAttributeLabel('financial_statuses') . '...',
                        ])
                        ->label($financialStatusesLabel) ?>
                </div>

                <div class="col-sm-12">
                    <?= $form
                        ->field($model, 'fulfillment_statuses')
                        ->checkboxList(ShopifyService::$fulfillmentStatuses, [
                            'itemOptions' => [
                                'labelOptions' => [
                                    'class' => 'font-weight-normal mr-1',
                                ],
                            ],
                            'placeholder' => $model->getAttributeLabel('fulfillment_statuses') . '...',
                        ])
                        ->label($fulfillmentStatusesLabel) ?>
                </div>

                <div class="col-sm-12">
                    <?= $form
                        ->field($model, 'customer_id')
                        ->dropdownList($customersList, [
                            'class' => 'form-control',
                            'prompt' => $model->getAttributeLabel('customer_id') . '...',
                            'placeholder' => $model->getAttributeLabel('customer_id') . '...',
                            'required' => true,
                            'maxlength' => true
                        ]) ?>
                </div>
                <div class="col-sm-12">
                    <?= Html::submitButton('Connect', [
                        'class' => 'btn btn-success',
                    ]) ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
