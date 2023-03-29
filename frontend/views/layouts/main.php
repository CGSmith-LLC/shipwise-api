<?php

/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php
$this->beginPage() ?>
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
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QWCXL2NN10"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-QWCXL2NN10');
    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script type="application/javascript">
        var stripe = Stripe('<?=Yii::$app->stripe->publicKey?>');
    </script>
    <script>
        (function (h, o, t, j, a, r) {
            h.hj = h.hj || function () {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {hjid: 2765222, hjsv: 6};
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>
    <script>
        window.fwSettings = {
            'widget_id': 150000002310
        };
        !function () {
            if ("function" != typeof window.FreshworksWidget) {
                var n = function () {
                    n.q.push(arguments)
                };
                n.q = [], window.FreshworksWidget = n
            }
        }()

        <?php
        if (!Yii::$app->user->isGuest) { ?>
        FreshworksWidget('identify', 'ticketForm', {
            'email': '<?= Yii::$app->user->identity->email ?>',
        })
        <?php
        }
        ?>

    </script>
    <script type='text/javascript' src='https://widget.freshworks.com/widgets/150000002310.js' async defer></script>
    <?php
    $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php
    $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

</head>
<body>
<?php
$this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Html::img(
            '@web/images/logo-with-text.png',
            ['alt' => Yii::$app->name, 'style' => 'height: 32px']
        ),
        'brandOptions' => ['class' => 'shipwise-brand'],
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-default navbar-fixed-top',
        ],
    ]);

    // this link allows admin to jump back when being impersonated as another person.
    /** @var Da\User\Module $module */
    $module = Yii::$app->getModule('user');
    if (Yii::$app->session->has($module->switchIdentitySessionKey)) {
        echo Html::a(
            '<span class="glyphicon glyphicon-user"></span> Back to original user',
            ['/user/admin/switch-identity'],
            ['class' => 'btn btn-primary pull-right', 'data-method' => 'POST', 'style' => 'margin:8px']
        );
    }

    $menuItems = [];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/user/register']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/user/login']];
    } else {
        $menuItems[] = [
            'label' => 'Orders',
            'url' => ['/order'],
            'items' => [
                ['label' => 'Orders', 'url' => ['/order']],
                ['label' => 'Batches', 'url' => ['/order/batch']],
                ['label' => 'Import', 'url' => ['/order/import']],
                ['label' => 'Bulk Edit', 'url' => ['/order/bulk-edit']],
                ['label' => 'Scheduling', 'url' => ['/order/scheduled']],
            ]
        ];
        $menuItems[] = ['label' => 'Reports', 'url' => ['/report']];
        $menuItems[] = ['label' => 'Support', 'url' => Url::to('https://support.getshipwise.com')];
        $menuItems[] = [
            'encode' => false,
            'label' => '<i class="fa fa-lightbulb-o"></i> Suggestions',
            'linkOptions' => ['target' => '_blank'],
            'url' => Url::to('https://shipwise.kampsite.co/')
        ];

        if (Yii::$app->user->identity->isAdmin) {
            $menuItems[] = [
                'label' => 'Admin',
                'url' => ['/'],
                'items' => [
                    ['label' => 'Countries', 'url' => ['/country']],
                    ['label' => 'States/Provinces', 'url' => ['/state']],
                    ['label' => 'Status', 'url' => ['/status']],
                    ['label' => 'Customers', 'url' => ['/customer']],
                    ['label' => 'Users', 'url' => ['/user/admin/']],
                    ['label' => 'Integrations', 'url' => ['/integration']],
                    ['label' => 'Behaviors', 'url' => ['/behavior']],
                    ['label' => 'Jobs', 'url' => ['/monitor/jobs']],
                ],
            ];
        }

        $menuItems[] = [
            'label' => '<img src="https://www.gravatar.com/avatar/' . md5(
                    Yii::$app->user->identity->email
                ) . '?s=24&d=mp" style="border-radius:50%"> ' . Yii::$app->user->identity->username,
            'url' => ['/user/settings/account'],
            'encode' => false,
            'items' => [
                ['label' => 'Account', 'url' => ['/user/settings/profile']],

                ['label' => 'Items', 'url' => ['/sku']],

                [
                    'label' => 'Billing',
                    'url' => ['/billing'],
                    'visible' => Yii::$app->user->identity->isDirectCustomer()
                ],
                [
                    'label' => Yii::t('app', 'API'),
                    'url' => '/api-consumer',
                ],
                [
                    'label' => Yii::t('app', 'Webhooks'),
                    'url' => '/webhook',
                ],
                [
                    'label' => '<li style="margin-top: -6px;">'
                        . Html::beginForm(['/user/logout'], 'post')
                        . Html::submitButton('Logout', ['class' => 'logout', 'style' => 'padding: 3px 20px;'])
                        . Html::endForm()
                        . '</li>',
                    'encode' => false,
                    'url' => '/user/logout',
                ],

            ]
        ];
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

<?php
$this->endBody() ?>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    // Setup notyf for use on any page
    var notyf = new Notyf();
</script>
</body>
</html>
<?php
$this->endPage() ?>
