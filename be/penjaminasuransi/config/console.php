<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$ini = @parse_ini_file('env/.env', true);
$consumers = require __DIR__ . '/consumers.php';
$db_integration = require __DIR__ . '/db_integrasi.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'jwt' => [
            'class' => 'Doco\components\DocoJwt',
            'key' => 'secret',
            'user' => 'secret'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'cacheRedis' => [
            'class' => yii\redis\Cache::class,
        ],
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'exportInterval' => 1,
                ],
            ],
        ],
        'db' => $db,
        'db_integration' => $db_integration,
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
                'import_data' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'import_data',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, // Use this if you don't want to create a queue on producing messages
                    ],
                ],
                'sync_data' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'sync_data',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, // Use this if you don't want to create a queue on producing messages
                    ],
                ],
                'sync_dokumen_eklaim' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'sync_dokumen_eklaim',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, 
                    ],
                ],
                'sync_integrasi_asuransi' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'sync_integrasi_asuransi',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, 
                    ],
                ],
                'sync_dokumen_resep_kronis' => [
                    'connection' => 'default',
                    'exchange_options' => [
                        'name' => 'sync_dokumen_resep_kronis',
                        'type' => 'direct',
                    ],
                    'queue_options' => [
                        'declare' => false, 
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
        'docoRest' => [
            'class' => \Doco\components\DocoRest::class
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
