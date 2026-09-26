<?php
$conf = @parse_ini_file('env/.env', true);

return [
    'class' => 'yii\db\Connection',
    'dsn' => isset($conf['dbslave']['conn_str']) ? $conf['dbslave']['conn_str']:'',
    'username' => isset($conf['dbslave']['user']) ? $conf['dbslave']['user'] :'',
    'password' => isset($conf['dbslave']['password']) ? $conf['dbslave']['password'] :'',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
          'class' => 'yii\db\pgsql\Schema',
          'defaultSchema' => 'public'
        ]
    ],
];