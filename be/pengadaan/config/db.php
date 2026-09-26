<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=192.168.200.254;port=5432;dbname=db_docohealth',
    'username' => 'postgres',
    'password' => 'bdgRkamil',
    'charset' => 'utf8',
    'schemaMap' => [
        'pgsql' => [
          'class' => 'yii\db\pgsql\Schema',
          'defaultSchema' => 'public' //specify your schema here, public is the default schema
        ]
    ], // PostgreSQL
];
