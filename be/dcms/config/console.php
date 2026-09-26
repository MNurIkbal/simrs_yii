<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$db_integration = require __DIR__ . '/db_integration.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
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
        'db_integration' => $db_integration,
    ],
    'params' => $params,
    
    'controllerMap' => [
        'migrate-log' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationNamespaces' => ['app\module\migrations\integrations'],
            'migrationPath' => ['@app/migrations/integrations'],
            'db' => 'db_integration',
        ]
    ],
    
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
