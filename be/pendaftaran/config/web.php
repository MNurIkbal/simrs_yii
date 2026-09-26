<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_integration = require __DIR__ . '/db_integrasi.php';
$ini = @parse_ini_file('env/.env', true);
$producers = require __DIR__ . '/producers.php';
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
        'dbslave' => [
            'class' => 'yii\db\Connection',
            'dsn' => isset($conf['dbslave']['conn_str']) ? $conf['dbslave']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($conf['dbslave']['user']) ? $conf['dbslave']['user'] :'postgres',
            'password' => isset($conf['dbslave']['password']) ? $conf['dbslave']['password'] :'',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ],
        ],
        'db_integration' =>$db_integration,
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
        'urlManagerFrontend' => [
            'class' => 'yii\web\urlManager',
            'baseUrl' => isset($ini['server']['url']) ? $ini['server']['url'] : '',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
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

return $config;
