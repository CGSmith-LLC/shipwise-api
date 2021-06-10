<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Customers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Customer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'address1',
            'address2',
            'city',
            //'state_id',
            //'zip',
            //'phone',
            //'email:email',
            //'logo',
            //'created_date',
            //'stripe_customer_id',
            //'direct',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
