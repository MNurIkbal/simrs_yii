<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$ini = @parse_ini_file('env/.env', true);

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Asia/Jakarta',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'docoIntegrasi' => [
            'class' => \Doco\components\DocoIntegrasi::class
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : 'localhost',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
        ],
        'docoRest' => [
            'class' => \Doco\components\DocoRest::class
        ],
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
