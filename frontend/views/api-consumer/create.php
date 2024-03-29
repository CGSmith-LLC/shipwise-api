<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApiConsumer */

$this->title = 'Create API Key';
$this->params['breadcrumbs'][] = ['label' => 'API Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-consumer-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
    ]) ?>

</div>
