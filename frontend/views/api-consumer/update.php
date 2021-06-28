<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApiConsumer */

$this->title = 'Update Api Consumer: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Api Consumers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="api-consumer-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
