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
$this->registerJs('
$(".masked-button").on("click", function(event){
    masked = $(event.target).prev();
    if ($(this).text() == "Show") {
        masked.html(masked.data("content"));
        $(this).text("Hide");
    } else {
        masked.html(masked.data("mask"));
        $(this).text("Show"); 
    }
});
');
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
            [
                'attribute' => 'customer',
                'value' => $model->customer->name
            ],
            'endpoint',
            [
                'attribute' => 'authentication_type',
                'value' => function ($model) {
                    return $model->authenticationOptions[$model->authentication_type];
                }
            ],
            'user',
            [
                'attribute' => 'pass',
                'format' => 'raw',
                'value' => function ($model) {
                    $button = Html::button('Show', ['class' => 'btn btn-sm masked-button']);
                    $html = Html::tag('div', $model->getMasked('pass'), ['class' => 'masked',  'data-mask' => $model->getMasked('pass'),'data-content' => $model->pass]);
                    return $html . $button;
                }
            ],
            [
                'attribute' => 'signing_secret',
                'format' => 'raw',
                'value' => function ($model) {
                    $button = Html::button('Show', ['class' => 'btn btn-sm masked-button']);
                    $html = Html::tag('div', $model->getMasked('signing_secret'), ['class' => 'masked', 'data-mask' => $model->getMasked('signing_secret'), 'data-content' => $model->signing_secret]);
                    return $html . $button;
                }
            ],
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
