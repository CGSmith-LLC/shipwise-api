<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

$appName = 'ShipWise';

return [
    'id' => 'app-frontend',
    'name' => $appName,
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'monitor'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            'languages' => false,
            'enablePrettyUrl' => true,
            'enableDefaultLanguageUrlCode' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'index' => 'site/index',
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'formatter' => [
            //'defaultTimeZone' => 'America/Chicago',
            'timeZone' => 'America/Chicago',
            'dateFormat' => 'php:m/d/Y',
            'datetimeFormat' => 'php:m/d/Y g:ia T',
            'timeFormat' => 'php:g:i:sa e',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@Da/User/resources/views' => '@frontend/views/user', // our overrides
                ],
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
        'monitor' => [
            'class' => \zhuravljov\yii\queue\monitor\Module::class,
        ],
        'user' => [
            'class' => Da\User\Module::class,
            'mailParams' => [
                'fromEmail' => [$params['senderEmail'] => $appName],
            ],
            'enableFlashMessages' => false,
            'administrators' => $params['adminEmail'],
            'controllerMap' => [
                'admin' => 'frontend\controllers\user\AdminController',
                'registration' => [
                    'class' => Da\User\Controller\RegistrationController::class,
                    'on ' . Da\User\Event\FormEvent::EVENT_AFTER_REGISTER => [
                        'frontend\events\user\AfterRegisterEvent',
                        'notifyAdmin'
                    ]
                ],
            ],
            'classMap' => [
                'User' => 'frontend\models\User',
                'RegistrationForm' => 'frontend\models\forms\RegistrationForm',
            ]
        ],
    ],
];
