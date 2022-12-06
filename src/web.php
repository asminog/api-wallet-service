<?php

$config = \yii\helpers\ArrayHelper::merge(require __DIR__ . '/config.php', [
    'controllerNamespace' => 'api\controllers',
    'components' => [
        'request'      => [
            'cookieValidationKey' => 'Rg0MNrI-VGbwNr6x4rc6BLDBv--06xhc',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
//            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                'POST v1/balance/<id>/<type>/<reason>' => 'v1/balance/update',
                'GET v1/balance/<id>' => 'v1/balance/view',
                'GET v1/balance/refund/7days' => 'v1/balance/refund-stat'
            ],
        ],
    ],
]);

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'       => 'yii\debug\Module',
        'allowedIPs'  => ['*'],
        'historySize' => 500,
    ];
}

return $config;