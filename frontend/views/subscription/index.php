<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Subscriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Subscription', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'columns' => [
            [
                'attribute' => 'customer_id',
                'value' => function ($model) {
                    return $model->customer->name;
                }
            ],
            'next_invoice',
            'months_to_recur',
            [
                'label' => 'Monthly Value',
                'value' => function ($model) {
                    /** @var \frontend\models\Subscription $model */
                    $total = $model->getItems()->sum('amount');
                    if ($model->months_to_recur > 1) {
                        $total = $total / $model->months_to_recur;
                    }
                    return ($total / 100);
                },
                'format' => 'currency',
                'footer' => Yii::$app->formatter->asCurrency(\frontend\models\Subscription::getTotal($dataProvider->models, 'amount')),
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
