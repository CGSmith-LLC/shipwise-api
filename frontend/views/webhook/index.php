<?php

use common\models\WebhookLog;
use yii\bootstrap\Modal;
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
                    $log = WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(
                        ['id' => SORT_DESC]
                    )->one();
                    return $log->getLabelFor('status_code');
                }
            ],
            [
                'attribute' => 'recentResponse',
                'format' => 'raw',
                'value' => function ($model) {
                    $log = WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(
                        ['id' => SORT_DESC]
                    )->one();
                   return $log->getModalForView();
                }
            ],
            //'customer_id',
            //'when',
            //'active',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
