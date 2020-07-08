<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\OneTimeCharge */

$this->title = 'Create One Time Charge';
$this->params['breadcrumbs'][] = ['label' => 'One Time Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="one-time-charge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
