<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Sku */
/* @var $customers array of customers */

$this->title = 'Create Sku';
$this->params['breadcrumbs'][] = ['label' => 'Sku', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sku-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
    ]) ?>

</div>
