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
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'customer_id',
            'ecommerce',
            'fulfillment',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
