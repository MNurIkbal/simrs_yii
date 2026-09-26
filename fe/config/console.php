<?php

$params = require __DIR__ . '/params.php';
// $db = require __DIR__ . '/db.php';

$ini = @parse_ini_file('env/.env', true);

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'docoRest' => [
            'class' => app\components\DocoRest::class
        ],
        'docoVars' => [
            'class' => app\components\Docovars::class
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'dsn' => isset($ini['database']['conn_str'])
                ? $ini['database']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_sirs',
            'username' => isset($ini['database']['user']) ? $ini['database']['user'] :'postgres',
            'password' => isset($ini['database']['password']) ? $ini['database']['password'] :'',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ],
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
