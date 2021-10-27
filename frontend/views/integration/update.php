<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Integration */
/* @var $customers array of customers */


$this->title = 'Update Integration: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Integrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="integration-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'ecommercePlatforms' => $ecommercePlatforms,
        'customers' => $customers,
    ]) ?>

</div>
