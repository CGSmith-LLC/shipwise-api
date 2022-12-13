<?php

use common\models\WebhookLog;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Webhook */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Webhooks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="webhook-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'endpoint',
            [
                'attribute' => 'authentication_type',
                'value' => function ($model) {
                    return $model->authenticationOptions[$model->authentication_type];
                }
            ],
            'user',
            'pass',
            'customer_id',
            [
                'attribute' => 'active',
                'value' => function ($model) {
                    return ($model->active) ? 'Enabled' : 'Disabled';
                }
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <?php

    $dataProvider = new ActiveDataProvider([
        'query' => WebhookLog::find()->where(['webhook_id' => $model->id])->orderBy(['id' => SORT_DESC]),
    ]);

    echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'created_at:datetime',
            [
                'attribute' => 'status_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getLabelFor('status_code');
                }
            ],
            [
                'attribute' => 'response',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getModalForView();
                }
            ],
        ]
    ]);
    ?>

</div>
