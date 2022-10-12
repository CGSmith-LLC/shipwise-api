<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AliasParent */

$this->title = 'Update Alias Parent: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Aliases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="alias-parent-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'customers' => $customers,
        'model' => $model,
    ]) ?>

</div>
