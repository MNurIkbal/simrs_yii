<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'Radiologi',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'serconn' => [
        'uri_api' => isset($conf['serconn']['url']) ? $conf['serconn']['url'] : 'http://127.0.0.1:8062',
    ]
];
