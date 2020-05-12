<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\PaymentMethod */
/* @var $customers frontend\models\User */

$this->title = 'Create Payment Method';
$this->params['breadcrumbs'][] = ['label' => 'Payment Methods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-method-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
