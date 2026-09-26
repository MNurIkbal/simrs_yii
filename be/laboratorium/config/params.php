<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'Laboratorium',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'serconn' => [
        'uri_api' => isset($conf['serconn']['url']) ? $conf['serconn']['url'] : 'http://127.0.0.1:8062',
    ],
    'iniFile' => $conf,
    'config-wynacom' => [
        'uri_api' => isset($conf['config-wynacom']['uri_api']) ? $conf['config-wynacom']['uri_api'] : '',
    ],

];
