<?php
$ini = @parse_ini_file(__DIR__ . '/../../dcms/config/env/.env', true);
return [
    'id' => 'service-connector',
    'basePath' => dirname(__DIR__),
    'components' => [
        'jwt' => [
            'class' => 'Doco\components\DocoJwt',
            'key' => 'secret',
            'user' => 'secret'
        ],
        'cache' => [
            'class' => yii\redis\Cache::class,
        ],
        'cacheFiles' => [
            'class' => yii\caching\FileCache::class,
            'cachePath' => dirname(__DIR__) .'/Runtime/Cache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : 'localhost',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => isset($ini['database']['conn_str']) ? $ini['database']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($ini['database']['user']) ? $ini['database']['user'] :'postgres',
            'password' => isset($ini['database']['password']) ? $ini['database']['password'] :'',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ]
        ],
        'dbslave' => [
            'class' => 'yii\db\Connection',
            'dsn' => isset($ini['dbslave']['conn_str']) ? $ini['dbslave']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($ini['dbslave']['user']) ? $ini['dbslave']['user'] :'postgres',
            'password' => isset($ini['dbslave']['password']) ? $ini['dbslave']['password'] :'',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ]
        ],
        'db_integration' => [
            'class' => 'yii\db\Connection',
            'dsn' => isset($ini['db_integration']['conn_str']) ? $ini['db_integration']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($ini['db_integration']['user']) ? $ini['db_integration']['user'] :'postgres',
            'password' => isset($ini['db_integration']['password']) ? $ini['db_integration']['password'] :'',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ]
        ],
        'docoRest' => [
            'class' => \Integrasi\Components\DocoRest::class
        ],
        'report' => [
            'class' => \Integrasi\Components\SirsReport::class
        ],
    ],
    'params' => [
        'first_key' => 'integerasi',
        'signature' => null,
        'service' => null,
        'connector' => isset($ini['serconn']['url']) ? $ini['serconn']['url'] :'',
        'serconn' => [
            'uri_api' => isset($ini['serconn']['url']) ? $ini['serconn']['url'] : 'http://127.0.0.1:8062',
        ],
        'serconn_ris' => [
            'uri_api' => isset($ini['serconn_ris']['url']) ? $ini['serconn_ris']['url'] : 'http://127.0.0.1:8062',
        ],
        'iniFile' => $ini,
        'config-wynacom' => [
            'uri_api' => isset($ini['config-wynacom']['uri_api']) ? $ini['config-wynacom']['uri_api'] : '',
        ],
        'pembantaran' => [
            'url' => isset($ini['pembantaran']['url']) ? $ini['pembantaran']['url'] : '',
        ],
    ],
];