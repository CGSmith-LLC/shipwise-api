<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Subscription */

$this->title = 'Create Subscription'
?>
<div class="subscription-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
