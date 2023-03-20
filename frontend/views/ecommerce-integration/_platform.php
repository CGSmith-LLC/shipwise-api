<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use common\models\EcommercePlatform;
use common\models\EcommerceIntegration;

/* @var $this View */
/* @var $model EcommercePlatform */

$disconnectConfirm = 'Are you sure you want to disconnect this platform?';
$disconnectConfirm .= ' In this case, all orders related to the platform will not be processed.';
$disconnectConfirm .= ' Also, you will lose all your current credentials and will need to connect this platform again.';

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
                if ($model->isActive()) {
                    $string = '<i class="glyphicon glyphicon-ok-circle text-success" title="Active"></i>';
                } else {
                    $string = '<i class="glyphicon glyphicon-ban-circle text-danger" title="Inactive"></i>';
                }

                return Html::encode($model->name) . ' ' . $string;
            },
        ],
        [
            'label' => 'Status:',
            'format' => 'raw',
            'value' => function($model) {
                /* @var $ecommerceIntegration EcommerceIntegration */
                $ecommerceIntegration = $model->ecommerceIntegration;

                $icon = '<i class="glyphicon glyphicon-ban-circle text-danger" title="' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED] . '"></i>';
                $string = '<span>' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED] . ' ' . $icon . '</span>';

                if ($ecommerceIntegration && $ecommerceIntegration->status != EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED) {
                    if ($ecommerceIntegration->isConnected()) {
                        $icon = '<i class="glyphicon glyphicon-ok-circle text-success" title="' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_CONNECTED] . '"></i>';
                        $string = '<span>' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_CONNECTED] . ' ' . $icon . '</span>';
                    } elseif ($ecommerceIntegration->isPaused()) {
                        $icon = '<i class="glyphicon glyphicon glyphicon-pause text-warning" title="' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_PAUSED] . '"></i>';
                        $string = '<span>' . EcommerceIntegration::getStatuses()[EcommerceIntegration::STATUS_INTEGRATION_PAUSED] . ' ' . $icon . '</span>';
                    }
                }

                return $string;
            },
        ],
        [
            'label' => 'Processed orders:',
            'format' => 'raw',
            'value' => function($model) {
                return 0;
            },
        ],
        [
            'label' => 'Pending orders:',
            'format' => 'raw',
            'value' => function($model) {
                return 0;
            },
        ],
        [
            'label' => 'Actions:',
            'format' => 'raw',
            'visible' => $model->isActive(),
            'value' => function($model) use ($pauseConfirm, $disconnectConfirm) {
                /* @var $ecommerceIntegration EcommerceIntegration */
                $ecommerceIntegration = $model->ecommerceIntegration;

                if (!$ecommerceIntegration) {
                    $status = EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED;
                } else {
                    $status = $ecommerceIntegration->status;
                }

                switch ($status) {
                    case EcommerceIntegration::STATUS_INTEGRATION_DISCONNECTED:
                        $url = Url::to(['/ecommerce-integration/connect', 'platform' => $model->name]);
                        $buttons = '<a href="' . $url . '" class="btn btn-success">Connect</a>';
                        break;
                    case EcommerceIntegration::STATUS_INTEGRATION_CONNECTED:
                        $url = Url::to(['/ecommerce-integration/pause', 'platform' => $model->name]);
                        $buttons = '<a href="' . $url . '" class="btn btn-warning" onclick="return confirm(\'' . $pauseConfirm . '\')">Pause</a>';

                        $url = Url::to(['/ecommerce-integration/disconnect', 'platform' => $model->name]);
                        $buttons .= ' <a href="' . $url . '" class="btn btn-danger" onclick="return confirm(\'' . $disconnectConfirm. '\')">Disconnect</a>';
                        break;
                    case EcommerceIntegration::STATUS_INTEGRATION_PAUSED:
                        $url = Url::to(['/ecommerce-integration/resume', 'platform' => $model->name]);
                        $buttons = '<a href="' . $url . '" class="btn btn-success">Resume</a>';

                        $url = Url::to(['/ecommerce-integration/disconnect', 'platform' => $model->name]);
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
