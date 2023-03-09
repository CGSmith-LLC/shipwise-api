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

    <?= Html::a('View Aliases', ['alias/index']); ?>
    <p>
        <?= Html::a('Create SKU', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Create Alias', ['alias/create'], ['class' => 'btn btn-info']) ?>
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
            'excluded:boolean',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
