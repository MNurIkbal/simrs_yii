<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$ini = @parse_ini_file('env/.env', true);
$config = [
    'id' => 'basic',
    'language' => 'id',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => app\modules\v1\Module::class,
            'defaultRoute' => 'v1',
        ],
    ],
    'container' => [
        'definitions' => [
            app\modules\v1\services\Contracts\BpjsInterface::class => app\modules\v1\services\BpjsService::class
        ],
    ],
    'components' => [
        'errorHandler' => [
            'class' => 'Doco\components\DocoErrorHandler'
        ],
        'jwt' => [
            'class' => 'Doco\components\DocoJwt',
            'key' => 'secret',
            'user' => 'secret'
        ],
        'request' => [
            'cookieValidationKey' => 'BrHTokPrh9SJv0MUgR1E2mmvFhgmFE_k',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => yii\redis\Cache::class,
        ],
        'user' => [
            'identityClass' => 'Doco\models\User',
            'enableSession' => false,
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => []
                ],
            ],
        ],
        'db' =>$db,
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : null,
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : null,
            'database' => 0
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
               ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ]
        ],
        'docoRest' => [
            'class' => \Doco\components\DocoRest::class
        ],
        'docoIntegrasi' => [
            'class' => \Doco\components\DocoIntegrasi::class
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : 'localhost',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'serconn' => [
            'class' => \Doco\components\DocoSerconn::class,
            'uri_api' => isset($ini['serconn']['url']) ? $ini['serconn']['url'] : 'http://192.168.200.239:8072',
        ],
        'docoPlugin' => [
            'class' => \Doco\components\DocoPlugin::class
        ],
    ],
    'params' => $params,
];

return $config;
