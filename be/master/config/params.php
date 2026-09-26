<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'Master',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    // 'enableAutoLogin' => true,
    'serconn' => [
        'uri_api' => isset($conf['serconn']['url']) ? $conf['serconn']['url'] : 'http://127.0.0.1:8062',
    ],
    'isRabbitMq' =>  isset($conf['rabbitMq']['host']) ? true : false,
    'expiration' => isset($conf['rabbitMq']['expiration']) ? $conf['rabbitMq']['expiration'] : null, // shortstr default ampq properties
    'prefix' => isset($conf['rabbitMq']['prefix']) ? $conf['rabbitMq']['prefix'] : '',
    'url_backend' => isset($conf['rabbitMq']['url_backend']) ? $conf['rabbitMq']['url_backend'] : '',
    'iniFile' => $conf,
];
