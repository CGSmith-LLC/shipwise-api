<?php

use frontend\models\forms\IntegrationForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IntegrationForm */
/* @var $customers array of customers */


$this->title = 'Create Integration';
$this->params['breadcrumbs'][] = ['label' => 'Integrations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="integration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'customers' => $customers,
    ]) ?>

</div>
