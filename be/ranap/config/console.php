<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$consumers = require __DIR__ . '/consumers.php';
$ini = @parse_ini_file('env/.env', true);

$config = [
    'id' => 'basic-console',
    'timeZone' => 'Asia/Jakarta',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'v1' => [
            'class' => app\modules\v1\Module::class,
            'defaultRoute' => 'v1',
        ],
    ],
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
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'Doco\models\User',
            'enableSession' => false,
            'enableAutoLogin' => false,
        ],
        'jwt' => [
            'class' => 'Doco\components\DocoJwt',
            'key' => 'secret',
            'user' => 'secret'
        ],
        'docoPlugin' => [
            'class' => \Doco\components\DocoPlugin::class
        ],
        'docoRest' => [
            'class' => \Doco\components\DocoRest::class
        ],
        // 'request' => [
        //     'class' => yii\web\Request::class,
        //     'cookieValidationKey' => 'BrHTokPrh9SJv0MUgR1E2mmvFhgmFE_k',
        //     'parsers' => [
        //         'application/json' => 'yii\web\JsonParser',
        //     ]
        // ],
        'rabbitmq'  => [
            'class' => 'mikemadisonweb\rabbitmq\Configuration',
            'connections' => [
                'default' => [
                    'host' => isset($ini['rabbitMq']['host']) ? $ini['rabbitMq']['host'] : 'rabbitmq',
                    'port' => isset($ini['rabbitMq']['port']) ? $ini['rabbitMq']['port'] : '5672',
                    'user' => isset($ini['rabbitMq']['user']) ? $ini['rabbitMq']['user'] : 'guest',
                    'password' => isset($ini['rabbitMq']['password']) ? $ini['rabbitMq']['password'] : 'guest',
                    'vhost' => isset($ini['rabbitMq']['vhost']) ? $ini['rabbitMq']['vhost'] : '/',
                    'heartbeat' => isset($ini['rabbitMq']['heartbeat']) ? $ini['rabbitMq']['heartbeat'] : 0,
                    'read_write_timeout' => isset($ini['rabbitMq']['read_write_timeout']) ? $ini['rabbitMq']['read_write_timeout'] : 60,
                    'connection_timeout' => isset($ini['rabbitMq']['connection_timeout']) ? $ini['rabbitMq']['connection_timeout'] : 60,
                ],
            ],
            'multipleConsumers' => $consumers,
            'producers' => [
                'import_data_cppt_pdf' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'import_data_cppt_pdf',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, // Use this if you don't want to create a queue on producing messages
                    ],
                ],
            ],
            'logger' => [
                'enable' => false,
                'category' => 'application',
                'print_console' => false,
                'system_memory' => false,
            ],
            'on before_consume' => function ($event) {
                if (isset(\Yii::$app->db)) {
                    $db = \Yii::$app->db;
                    if ($db->getIsActive()) {
                        $db->close();
                    }
                    $db->open();
                }
                if (isset(\Yii::$app->redis)) {
                    $redis = \Yii::$app->redis;
                    if ($redis->getIsActive()) {
                        $redis->close();
                    }
                    $redis->open();
                }
            },
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : 'localhost',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
        ],
    ],
    'params' => $params,
    'controllerMap' => [
        'rabbitmq-consumer' => \mikemadisonweb\rabbitmq\controllers\ConsumerController::class,
        'rabbitmq-producer' => \mikemadisonweb\rabbitmq\controllers\ProducerController::class,
    ],

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
