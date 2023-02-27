<?php
use yii\web\View;
use yii\helpers\Html;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $model EcommercePlatform */

$title = 'Update ' . $model->name;
$this->title = $title . ' - E-commerce Platforms - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = ['label' => 'E-commerce Platforms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h1 class="panel-title">
            <?= Html::encode($title) ?>
        </h1>
    </div>

    <div class="panel-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
