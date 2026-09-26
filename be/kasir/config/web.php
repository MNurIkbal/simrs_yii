<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$dbslave = require __DIR__ . '/dbslave.php';
$ini = @parse_ini_file('env/.env', true);
$producers = require __DIR__ . '/producers.php';
$config = [
    'id' => 'basic',
    'language' => 'id',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'v1' => [
            'class' => app\modules\v1\Module::class,
            'defaultRoute' => 'v1',
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
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
        'dbslave' =>$dbslave,
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
        'docoPlugin' => [
            'class' => \Doco\components\DocoPlugin::class
        ],
        'kasirCache' => [
            'class' => \app\modules\v1\cache\Cache::class
        ],
        'kasirHelper' => [
            'class' => \app\modules\v1\businessLogic\TagihanHelper::class
        ],
        'rabbitmq'  => [
            'class' => 'mikemadisonweb\rabbitmq\Configuration',
            'connections' => [
                'default' => [
                    'host' => isset($ini['rabbitMq']['host']) ? $ini['rabbitMq']['host'] : 'rabbitmq',
                    'port' => isset($ini['rabbitMq']['port']) ? $ini['rabbitMq']['port'] : '5672',
                    'user' => isset($ini['rabbitMq']['user']) ? $ini['rabbitMq']['user'] : 'guest',
                    'password' => isset($ini['rabbitMq']['password']) ? $ini['rabbitMq']['password'] : 'guest',
                    'vhost' => isset($ini['rabbitMq']['vhost']) ? $ini['rabbitMq']['vhost'] : '/',
                    'heartbeat' => isset($ini['rabbitMq']['heartbeat']) ? $ini['rabbitMq']['heartbeat'] : '0',
                ],
            ],
            'producers' => $producers
        ],
        // */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
