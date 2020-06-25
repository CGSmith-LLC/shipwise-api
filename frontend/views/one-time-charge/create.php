<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\OneTimeCharge */

$this->title = 'Create One Time Charge';
?>
<div class="one-time-charge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
