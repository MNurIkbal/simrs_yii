<?php
$conf = @parse_ini_file('env/.env', true);

return [
    'class' => 'yii\db\Connection',
    'dsn' => isset($conf['db_integration']['conn_str'])
                     ? $conf['db_integration']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_name',
    'username' => isset($conf['db_integration']['user']) ? $conf['db_integration']['user'] :'postgres',
    'password' => isset($conf['db_integration']['password']) ? $conf['db_integration']['password'] :'',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    'schemaCacheDuration' => 3600,
    'schemaMap' => [
        'pgsql' => [
          'class' => 'yii\db\pgsql\Schema',
          'defaultSchema' => 'public'
        ]
    ]
];
