<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Aliases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alias-parent-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= Html::a('View Items', ['sku/index']);?>
    <p>
        <?= Html::a('Create SKU', ['sku/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create Alias', ['create'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'customer.name',
                // Visible if admin or has a count higher than 0 for associated users
                'visible' => ((count($customerDropdownList) > 1) || Yii::$app->user->identity->getIsAdmin()),
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'customer_id',
                    $customerDropdownList,
                    ['class' => 'form-control', 'prompt' => Yii::t('app', 'All Customers')]
                ),
            ],
            'sku',
            'name',
            [
                'attribute' => 'Children',
                'format' => 'raw',
                'value' => function($data) {
                    $raw = '';
                    foreach ($data->items as $item) {
                        $raw .= '<span class="label label-info">'.$item->quantity.' x '.$item->sku.'</span> ';
                    }
                    return $raw;
                }
            ],
            [
                'attribute' => 'active',
                'format' => 'boolean',
                'filter' => [0 => 'No', 1 => 'Yes'],
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
