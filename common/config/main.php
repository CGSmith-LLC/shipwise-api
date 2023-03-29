<?php

$params = array_merge(
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'

);

$appName = 'Shipwise';

return [
    'name' => $appName,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'queue', // The component registers its own console commands
    ],
    'container' => [
        'singletons' => [
            \zhuravljov\yii\queue\monitor\Env::class => [
                'cache' => 'cache',
                'db' => 'db',
                'pushTableName'   => '{{%queue_push}}',
                'execTableName'   => '{{%queue_exec}}',
                'workerTableName' => '{{%queue_worker}}',
            ],
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'storage' => [
            'class' => 'bilberrry\spaces\Service',
            'credentials' => [
                'key' => $params['digitalOceanKey'],
                'secret' => $params['digitalOceanSecret'],
            ],
            'region' => 'nyc3',
            'defaultSpace' => $params['defaultSpace'],
            'defaultAcl' => 'public-read',
        ],
        'csvboxstorage' => [
            'class' => 'frostealth\yii2\aws\s3\Service',
            'credentials' => [ // Aws\Credentials\CredentialsInterface|array|callable
                'key' => $params['csvBoxS3Key'],
                'secret' => $params['csvBoxS3Secret'],
            ],
            'region' => $params['csvBoxS3Region'],
            'defaultBucket' => $params['csvBoxS3Bucket'],
            'defaultAcl' => 'public-read',
        ],
        'customerSettings' => [
            'class' => 'common\components\CustomerSettings',
        ],
        'queue' => [
            'class' => 'yii\queue\db\Queue',
            'db' => 'db', // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => 'yii\mutex\MysqlMutex', // Mutex used to sync queries
            'as log' => 'yii\queue\LogBehavior',
            //'as deadLetterBehavior' => \common\behaviors\DeadLetterQueue::class,
            'as jobMonitor' => \zhuravljov\yii\queue\monitor\JobMonitor::class,
            'as workerMonitor' => \zhuravljov\yii\queue\monitor\WorkerMonitor::class,
            'ttr' => 5 * 60, // Max time for anything job handling
            'attempts' => 3, // Max number of attempts
            'on afterError' => function (yii\queue\ExecEvent $event) {
                if ($event->job instanceof \console\jobs\CreateReportJob) {
                    if ($event->job->isFailed($event->attempt)) {
                        \console\jobs\CreateReportJob::sendFailEmail($event->job->user_email, $event->job->customer);
                    }
                }
            },
        ],
        'fulfillment' => function () {
            return new \common\components\FulfillmentService();
        },
        'coldco' => [
            'class' => 'common\components\ColdcoFulfillmentService',
            'baseUrl' => $params['coldco']['baseUrl'],
            'clientId' => $params['coldco']['clientId'],
            'secret' => $params['coldco']['secret'],
        ]
    ],
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,
            'enableTwoFactorAuthentication' => true,
            'mailParams' => [
                'fromEmail' => [$params['senderEmail'] => $appName],
            ],
            'enableFlashMessages' => false,
            'administrators' => $params['adminEmail'],
            'controllerMap' => [
                'admin' => 'frontend\controllers\user\AdminController',
                'registration' => [
                    'class' => frontend\controllers\user\RegistrationController::class,
                    'on ' . Da\User\Event\FormEvent::EVENT_AFTER_REGISTER => [
                        'frontend\events\user\AfterRegisterEvent',
                        'notifyAdmin'
                    ]
                ],
            ],
            'classMap' => [
                'User' => 'frontend\models\User',
                'RegistrationForm' => 'frontend\models\forms\RegistrationForm',
                'Profile' => 'frontend\models\Profile',
            ]
        ],
    ],
];
