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
                'visible' => Yii::$app->user->identity->isAdmin || (is_countable(Yii::$app->user->identity->customerIds) ? count(Yii::$app->user->identity->customerIds) : 0) > 1,
            ],
            'name',
            [
                'attribute' => 'endpoint',
                'format' => 'raw',
                'value' => fn($model) => $model->endpoint . ' ' . $model->getLabelFor('authentication_type')
            ],
            [
                'attribute' => 'recentStatusCode',
                'format' => 'raw',
                'value' => function ($model) {
                    $log = WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(
                        ['id' => SORT_DESC]
                    )->one();
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
                    $log = WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(
                        ['id' => SORT_DESC]
                    )->one();
                    if ($log) {
                        return $log->getModalForView();
                    } else {
                        return '';
                    }
                }
            ],
            [
                'attribute' => 'active',
                'value' => fn($model) => $model->getLabelFor('active'),
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
