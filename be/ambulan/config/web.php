<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$conf = @parse_ini_file('env/.env', true);
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
        'docoRest' => [
            'class' => \Doco\components\DocoRest::class
        ],
        'request' => [
            'cookieValidationKey' => 'BrHTokPrh9SJv0MUgR1E2mmvFhgmFE_k',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'redis',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
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
        'db' =>[
            'class' => 'yii\db\Connection',
            'dsn' => isset($conf['database']['conn_str']) ? $conf['database']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
            'username' => isset($conf['database']['user']) ? $conf['database']['user'] :'postgres',
            'password' => isset($conf['database']['password']) ? $conf['database']['password'] :'',
            'charset' => 'utf8',
            'schemaMap' => [
                'pgsql' => [
                  'class' => 'yii\db\pgsql\Schema',
                  'defaultSchema' => 'public'
                ]
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
               ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
            ]
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
