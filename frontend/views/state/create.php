<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\State */
/* @var $countries array list of countries */

$this->title = 'Create State';
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="state-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'countries' => $countries,
    ]) ?>

</div>
