<?php

use common\models\Customer;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'API';
$this->params['breadcrumbs'][] = $this->title;
if ((!Yii::$app->user->identity->getIsAdmin())) {
    $customerDropdownList = Yii::$app->user->identity->getCustomerList();
} else {
    $customerDropdownList = Customer::getList();
}
?>
<div class="api-consumer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create API Key', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-book"></i> Documentation', \yii\helpers\Url::to('https://api.getshipwise.com'), ['target' => '_blank', 'class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            'label',
            [
                'attribute' => 'auth_key',
                'label' => 'API Key',
            ],
            [
                'attribute' => 'encrypted_secret',
                'label' => 'API Password',
                //'class' => 'hidden',
                'value' => function($model) {
                    $secret = Yii::$app->getSecurity()->decryptByKey(base64_decode($model->encrypted_secret), Yii::$app->params['encryptionKey']);
                    return substr($secret, 0,3) .  '...' . substr($secret, -3);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]); ?>
</div>
