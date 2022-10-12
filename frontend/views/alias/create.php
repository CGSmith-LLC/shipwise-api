<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AliasParent */

$this->title = 'Create Alias Parent';
$this->params['breadcrumbs'][] = ['label' => 'Alias Parents', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alias-parent-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'customers' => $customers,
        'model' => $model,
    ]) ?>

</div>
