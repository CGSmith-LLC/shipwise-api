<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EcommerceIntegration;
use common\services\platforms\ShopifyService;

/* @var $this View */
/* @var $model EcommerceIntegration */

$disconnectConfirm = 'Are you sure you want to disconnect this platform?';
$disconnectConfirm .= ' In this case, all orders related to the platform will not be processed.';
$disconnectConfirm .= ' Also, you will lose all your current credentials and will need to reconnect this platform again.';

$pauseConfirm = 'Are you sure you want to pause this platform?';
$pauseConfirm .= ' In this case, all orders with the platform will not be processed.';
?>

<?= DetailView::widget([
    'model' => $model,
    'options' => [
        'id' => $model->id,
        'class' => 'table table-striped table-bordered detail-view'
    ],
    'attributes' => [
        [
            'label' => 'Platform:',
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function($model) {
                if ($model->ecommercePlatform->isActive()) {
                    $string = '<i class="glyphicon glyphicon-ok-circle text-success" title="Active"></i>';
                } else {
                    $string = '<i class="glyphicon glyphicon-ban-circle text-danger" title="Inactive"></i>';
                }

                return Html::encode($model->ecommercePlatform->name) . ' ' . $string;
            },
        ],
        [
            'label' => 'Status:',
            'format' => 'raw',
            'value' => function($model) {
                if ($model->isConnected()) {
                    $icon = '<i class="glyphicon glyphicon-ok-circle text-success" title="' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_CONNECTED] . '"></i>';
                    $string = '<span>' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_CONNECTED] . ' ' . $icon . '</span>';
                } elseif ($model->isPaused()) {
                    $icon = '<i class="glyphicon glyphicon glyphicon-pause text-warning" title="' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_PAUSED] . '"></i>';
                    $string = '<span>' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_PAUSED] . ' ' . $icon . '</span>';
                } else {
                    $string = null;
                }

                return $string;
            },
        ],
        [
            'label' => 'Shop name:',
            'format' => 'raw',
            'value' => function($model) {
                $name = isset($model->array_meta_data['shop_name']) ? Html::encode($model->array_meta_data['shop_name']) : null;
                return $name;
            },
        ],
        [
            'label' => 'Shop URL:',
            'format' => 'raw',
            'value' => function($model) {
                return '<a href="https://'. $model->array_meta_data['shop_url'] .'" target="_blank">' . $model->array_meta_data['shop_url'] . ' </a>';
            },
        ],
        [
            'label' => 'Order Statuses:',
            'format' => 'raw',
            'value' => function($model) {
                if ($model->isMetaKeyExistsAndNotEmpty('order_statuses')) {
                    return implode(', ', array_map(function ($el) {
                        return ShopifyService::$orderStatuses[$el];
                    }, $model->array_meta_data['order_statuses']));
                }

                return 'Any';
            },
        ],
        [
            'label' => 'Financial Statuses:',
            'format' => 'raw',
            'value' => function($model) {
                if ($model->isMetaKeyExistsAndNotEmpty('financial_statuses')) {
                    return implode(', ', array_map(function ($el) {
                        return ShopifyService::$financialStatuses[$el];
                    }, $model->array_meta_data['financial_statuses']));
                }

                return 'Any';
            },
        ],
        [
            'label' => 'Fulfillment Statuses:',
            'format' => 'raw',
            'value' => function($model) {
                if ($model->isMetaKeyExistsAndNotEmpty('fulfillment_statuses')) {
                    return implode(', ', array_map(function ($el) {
                        return ShopifyService::$fulfillmentStatuses[$el];
                    }, $model->array_meta_data['fulfillment_statuses']));
                }

                return 'Any';
            },
        ],
        [
            'label' => 'Customer:',
            'format' => 'raw',
            'value' => function($model) {
                return Html::encode($model->customer->name);
            },
        ],
        [
            'label' => 'Connected:',
            'attribute' => 'created_date',
            'format' => 'datetime',
        ],
        [
            'label' => 'Actions:',
            'format' => 'raw',
            'visible' => $model->ecommercePlatform->isActive(),
            'value' => function($model) use ($pauseConfirm, $disconnectConfirm) {
                $status = $model->status;

                switch ($status) {
                    case EcommerceIntegration::STATUS_INTEGRATION_CONNECTED:
                        $url = Url::to(['/ecommerce-integration/pause', 'id' => $model->id]);
                        $buttons = '<a href="' . $url . '" class="btn btn-warning" onclick="return confirm(\'' . $pauseConfirm . '\')">Pause</a>';

                        $url = Url::to(['/ecommerce-integration/disconnect', 'id' => $model->id]);
                        $buttons .= ' <a href="' . $url . '" class="btn btn-danger" onclick="return confirm(\'' . $disconnectConfirm. '\')">Disconnect</a>';
                        break;
                    case EcommerceIntegration::STATUS_INTEGRATION_PAUSED:
                        $url = Url::to(['/ecommerce-integration/resume', 'id' => $model->id]);
                        $buttons = '<a href="' . $url . '" class="btn btn-success">Resume</a>';

                        $url = Url::to(['/ecommerce-integration/disconnect', 'id' => $model->id]);
                        $buttons .= ' <a href="' . $url . '" class="btn btn-danger" onclick="return confirm(\'' . $disconnectConfirm . '\')">Disconnect</a>';
                        break;
                    default:
                        $buttons = '';
                }

                return $buttons;
            },
        ],
    ],
]) ?>
