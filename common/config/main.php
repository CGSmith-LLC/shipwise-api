<?php
return [
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => [
        'queue', // The component registers its own console commands
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'customerSettings' => [
            'class' => 'common\components\CustomerSettings',
        ],

        'queue' => [
            'class'     => 'yii\queue\db\Queue',
            'db'        => 'db', // DB connection component or its config
            'tableName' => '{{%queue}}', // Table name
            'channel'   => 'default', // Queue channel key
            'mutex'     => 'yii\mutex\MysqlMutex', // Mutex used to sync queries
            'as log'    => 'yii\queue\LogBehavior',
        ],

    ],
];
