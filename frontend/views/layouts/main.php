<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="57x57" href="/images/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/images/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/images/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/images/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/images/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/images/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="manifest" href="/images/manifest.json">
    <meta name="msapplication-TileColor" content="#2c9fd6">
    <meta name="msapplication-TileImage" content="/images/ms-icon-144x144.png">
    <meta name="theme-color" content="#2c9fd6">

    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img('@web/images/logo.jpg', ['alt' => Yii::$app->name]),
        'brandOptions' => ['class' => 'shipwise-brand'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
        ],
    ]);

    // this link allows admin to jump back when being impersonated as another person.
    if (Yii::$app->session->has(\dektrium\user\controllers\AdminController::ORIGINAL_USER_SESSION_KEY)) {
        echo Html::a(
            '<span class="glyphicon glyphicon-user"></span> Back to original user',
            ['/user/admin/switch'],
            ['class' => 'btn btn-primary pull-right', 'data-method' => 'POST', 'style' => 'margin:8px']);
    }

    $menuItems = [];
    if (Yii::$app->user->isGuest) {

        $menuItems[] = ['label' => 'Signup', 'url' => ['/user/register']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/login']];

    } else {

        $menuItems = [
            ['label' => 'Home', 'url' => ['/']],
        ];
        $menuItems[] = ['label' => 'Orders', 'url' => ['/order']];

        if (Yii::$app->user->identity->isAdmin) {
            $menuItems[] = ['label' => 'Users', 'url' => ['/user/admin/']];
            $menuItems[] = [
                    'label' => 'Subscriptions', 'url' => ['/subscription'],
                    'items' => [
                    ['label' => 'Subscriptions', 'url' => ['/subscription']],
                    ['label' => 'One Time Charges', 'url' => ['/one-time-charge']],
                ]];
        }

        $menuItems[] = ['label' => 'Account', 'url' => ['/user/settings/account']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/user/logout'], 'post')
            . Html::submitButton('<img src="https://www.gravatar.com/avatar/' . md5(Yii::$app->user->identity->email) . '?s=24&d=mp" style="border-radius:50%">' .
                ' Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'logout']
            )
            . Html::endForm()
            . '</li>';

    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

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

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
