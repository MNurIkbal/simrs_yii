<?php
$conf = @parse_ini_file('env/.env', true);

return [
    'class' => 'yii\db\Connection',
    'dsn' => isset($conf['database']['conn_str']) ? $conf['database']['conn_str']:'pgsql:host=localhost;port=5432;dbname=db_docohealth',
    'username' => isset($conf['database']['user']) ? $conf['database']['user'] :'postgres',
    'password' => isset($conf['database']['password']) ? $conf['database']['password'] :'postgres',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
          'class' => 'yii\db\pgsql\Schema',
          'defaultSchema' => 'public'
        ]
    ],
];
