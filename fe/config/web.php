<?php

$modules = require __DIR__ . '/modules.php';
$params = require __DIR__ . '/params.php';
$ini = @parse_ini_file('env/.env', true);

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'language' => 'id',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'container' => [
        'definitions' => [
            app\components\Services\Contracts\BpjsConfigInterface::class => app\components\Services\BpjsConfigService::class,
            app\components\Services\Contracts\BpjsInterface::class => app\components\Services\BpjsService::class,
            app\components\Services\Contracts\RencanaKontrolInterface::class => app\components\Services\BpjsService::class,
            app\components\Services\Contracts\DiagnosaInterface::class => app\components\Services\Rm\DiagnosaService::class,
        ]
    ],
    'modules' => $modules,
    'components' => [
        // gara gara ini neh bootstarp ga muncul XD
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null,
                    'js' => []
                ],
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => isset($ini['redis']['hostname']) ? $ini['redis']['hostname'] : 'localhost',
            'port' => isset($ini['redis']['port']) ? $ini['redis']['port'] : 6379,
            'database' => isset($ini['redis']['database']) ? $ini['redis']['database'] : 0,
        ],
        'superset' => [
            'class' => app\components\Superset::class,
            'domain' => isset($ini['superset']['domain']) ? $ini['superset']['domain'] : '',
            'address' => isset($ini['superset']['address']) ? $ini['superset']['address'] : 'http://localhost:8080',
            'username' => isset($ini['superset']['username']) ? $ini['superset']['username'] : 'user',
            'password' => isset($ini['superset']['password']) ? $ini['superset']['password'] : 'password',
            'enabled' => isset($ini['superset']['enabled']) ? $ini['superset']['enabled'] : false,
        ],
        'request' => [
            'cookieValidationKey' => 'y2slVhLP3IIIn1hfMm9AiGNeKHYtemE7',
            // 'cookieValidationKey' => app\components\DocoCookie::class,
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'cache' => [
            'class' => yii\redis\Cache::class,
        ],
        'docoPlugin' => [
            'class' => app\components\DocoPlugin::class
        ],
        'docoRest' => [
            'class' => app\components\DocoRest::class
        ],
        'docoVars' => [
            'class' => app\components\DocoVars::class
        ],
        'docoFTP' => [
            'class' => app\components\DocoFTP::class
        ],
        'user' => [
            'identityClass' => app\models\User::class,
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            'maxSourceLines' => 20,
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logVars' => []
                ],
            ],
        ],
        'db' =>[
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
        'report' => [
            'class' => app\components\SirsReport::class
        ],
        'session' => [
            'class' => app\components\InterceptedSession::class,
            'name' => isset($conf['session']['session_name']) ? $conf['session']['session_name'] : 'development-sirs',
            'savePath' => sys_get_temp_dir(),
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [                

                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>/<flag:\d+>' => '<controller>/<action>',
                [
                    'pattern' => 'reports/viewer/<modules>/<controller>/<action>',
                    'route' => 'reports/viewer'
                ],
                [
                    'pattern' => 'reports/viewer/<kode>',
                    'route' => 'reports/by-code'
                ],
                //create jump to rule
                'jumpto/<slug:\w+>/<instalationid:\d+>/<roomid:\d+>/<target:[A-Za-z0-9\=%\-_]+>' => 'access-helper/jump-to',
                'opento/<slug:\w+>/<instalationid:\d+>/<roomid:\d+>/<target:[A-Za-z0-9\=%\-_]+>' => 'access-helper/open-to',
            ],
        ],
        'i18n' => [
            'translations' => [
                'fe*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => __DIR__ . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR . 'lang',
                    // 'sourceLanguage' => 'ID',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
            ],
        ],
    ],

    ],
    'params' => $params,
];


if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    // $config['bootstrap'][] = 'debug';
    // $config['modules']['debug'] = [
        // 'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    // ];

    // $config['bootstrap'][] = 'gii';
    // $config['modules']['gii'] = [
    //     'class' => 'yii\gii\Module',
    //     'allowedIPs' => ['*'],
    // ];
}

return $config;
