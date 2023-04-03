<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Integrations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integration-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Integration', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            [
                'value' => 'customer.name',
                'label' => 'Customer',
            ],
            'ecommerce',
            'fulfillment',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => fn($model) => $model->getStatusLabel()
            ],
            'status_message',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {change-status}',
                'buttons' => [
                    'change-status' => function ($url, $model, $key) {
                        if (Yii::$app->user->identity->getIsAdmin()) {
                            return \yii\bootstrap\ButtonDropdown::widget([
                                'label' => 'Change Status',
                                'options' => ['class' => 'btn-primary btn-sm'],
                                'dropdown' => [
                                    'items' => $model->generateActionList(),
                                ],
                            ]);
                        }
                        return '';// return nothing if not admin
                    },
                ]
            ],
        ],
    ]); ?>
</div>
