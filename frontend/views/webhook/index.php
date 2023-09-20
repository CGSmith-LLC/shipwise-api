<?php

use common\models\WebhookLog;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Webhooks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="webhook-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Webhook', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'customer.name',
                'label' => 'Customer',
                'visible' => Yii::$app->user->identity->isAdmin || count(Yii::$app->user->identity->customerIds) > 1,
            ],
            'name',
            [
                'attribute' => 'endpoint',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->endpoint . ' ' . $model->getLabelFor('authentication_type');
                }
            ],
            [
                'attribute' => 'recentStatusCode',
                'format' => 'raw',
                'value' => function ($model) {
                    $log = $model->lastWebhookLog;
                    if ($log) {
                        return $log->getLabelFor('status_code');
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'recentResponse',
                'format' => 'raw',
                'value' => function ($model) {
                    $log = $model->lastWebhookLog;
                    if ($log) {
                        return $log->getModalForView();
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'active',
                'value' => function ($model) { return $model->getLabelFor('active');},
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
