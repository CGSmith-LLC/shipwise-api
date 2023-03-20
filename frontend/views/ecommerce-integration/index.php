<?php
use yii\web\View;
use yii\helpers\Html;
use common\models\EcommercePlatform;

/* @var $this View */
/* @var $models EcommercePlatform[] */

$title = 'Ecommerce Integrations';
$this->title = $title . ' - ' . Yii::$app->name;
$this->params['breadcrumbs'][] = $title;
?>

<div>
    <h1>
        <?= Html::encode($title) ?>
    </h1>

    <?php foreach ($models as $model) { ?>
        <?= $this->render('_platform', [
            'model' => $model,
        ]) ?>
    <?php } ?>
</div>
