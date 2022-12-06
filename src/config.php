<?php

return [
    'id'                  => 'micro-api-app',
    'basePath'            => __DIR__,
    'aliases'             => [
        '@api'    => __DIR__,
        '@vendor' => dirname(__DIR__) . '/vendor',
        '@bower'  => '@vendor/bower-asset',
    ],
    'bootstrap' => [
        function (\yii\base\Application $app) {
            $app->db->createCommand("SET SESSION sql_mode = 'TRADITIONAL'")->execute();
        },
    ],
    'components'          => [
        'db'           => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=mysql;dbname=api',
            'username' => 'root',
            'password' => '',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log'          => [
            'traceLevel' => 3,
            'targets'    => [
                [
                    'class'          => 'yii\log\FileTarget',
                    'levels'         => ['error', 'warning'],
                    'exportInterval' => 1,
                ],
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Version1',
        ],
    ],
];