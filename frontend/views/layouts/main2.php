<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
$this->beginPage() ?>
<html lang="<?= Yii::$app->language ?>" class="h-full bg-white">
<head>
    <?php $this->head() ?>
</head>
<body class="h-full">

<?php $this->beginBody() ?>
<div class="container">

</div>

<div class="drawer">
    <input id="my-drawer" type="checkbox" class="drawer-toggle" />
    <ul class="menu p-4 w-80 bg-base-100 text-base-content">
        <li>hey</li>
    </ul>

</div>
<div class="wrap">


    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"></p>
    </div>
</footer>

<?php
$this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
