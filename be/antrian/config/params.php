<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'Antrian',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'enableAutoLogin' => true,
];
