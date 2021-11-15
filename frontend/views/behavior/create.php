<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Behavior */

$this->title = Yii::t('app', 'Create Behavior');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Behaviors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="behavior-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
