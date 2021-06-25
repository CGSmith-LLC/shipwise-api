<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Customer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Customers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="customer-view">

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
            'id',
            'name',
            'address1',
            'address2',
            'city',
            [
                'attribute' => 'state_id',
                'label' => 'State',
                'value' => \common\models\State::findOne($model->state_id)->name,
            ],
            'zip',
            'phone',
            'email:email',
            [
                    'attribute' => 'logo',
                'format' => 'html',
                'value' => Html::img($model->logo),
            ],
            'created_date:date',
            'stripe_customer_id',
            'direct:boolean'
        ],
    ]) ?>

</div>
