<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Customer;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
if ((!Yii::$app->user->identity->getIsAdmin())) {
    $customerDropdownList = Yii::$app->user->identity->getCustomerList();
} else {
    $customerDropdownList = Customer::getList();
}
?>
<div class="country-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sku', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'sku',
            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
