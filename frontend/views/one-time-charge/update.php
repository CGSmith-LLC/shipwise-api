<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\OneTimeCharge */

$this->title = 'Update One Time Charge: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'One Time Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="one-time-charge-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
