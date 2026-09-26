<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$ini = @parse_ini_file('env/.env', true);
$producers = require __DIR__ . '/producers.php';
$db_integration = require __DIR__ . '/db_integrasi.php';

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
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => isset($ini['database']['conn_str']) ? $ini['database']['conn_str'] : 'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($ini['database']['user']) ? $ini['database']['user'] : 'postgres',
            'password' => isset($ini['database']['password']) ? $ini['database']['password'] : '',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => [
                    'class' => 'yii\db\pgsql\Schema',
                    'defaultSchema' => 'public'
                ]
            ],
        ],
        'db_integration' => $db_integration,
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
        'assuransiClient' => [
            'class' => Doco\Libraries\Asuransi\AsuransiClient::class,
            'provider' => [
                'Apln' => Doco\Libraries\Asuransi\Client\Apln\AplnLogic::class,
                'Mcare' => Doco\Libraries\Asuransi\Client\Mcare\McareLogic::class
            ]
        ],
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
