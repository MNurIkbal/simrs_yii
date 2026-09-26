<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'Kasir',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'isRabbitMq' =>  isset($conf['rabbitMq']['host']) ? true : false,
    'expiration' => isset($conf['rabbitMq']['expiration']) ? $conf['rabbitMq']['expiration'] : null, // shortstr default ampq properties
    'prefix' => isset($conf['rabbitMq']['prefix']) ? $conf['rabbitMq']['prefix'] : '',
    'url_backend' => isset($conf['rabbitMq']['url_backend']) ? $conf['rabbitMq']['url_backend'] : '',
    'goexcelexporter' => [
        'uri_api' => isset($conf['goexcelexporter']['url']) ? $conf['goexcelexporter']['url'] : 'http://goexcelexporter:4646',
    ],
];
