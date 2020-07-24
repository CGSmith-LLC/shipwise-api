<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\forms\OrderForm */
/* @var $customers array List of customers */
/* @var $statuses array List of order statuses */
/* @var $carriers array List of carriers */
/* @var $services array List of carrier services */
/* @var $states array List of states */
/* @var $countries array List of states */

$this->title = 'Create Order';
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'     => $model,
        'customers' => $customers,
        'statuses'  => $statuses,
        'carriers'  => $carriers,
        'services'  => $services,
        'states'    => $states,
        'countries' => $countries,
    ]) ?>

</div>
