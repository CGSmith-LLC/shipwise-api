<?php

use common\models\Status;
use common\models\Webhook;
use common\models\WebhookTrigger;
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
            'endpoint',
            'authentication_type',
            'user',
            'pass',
            [
                'attribute' => 'lastEvent',
                'value' => function ($model) {
                    $log = \common\models\WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();
                    return $log->status_code;
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
