<?php

$conf = @parse_ini_file('env/.env', true);

return [
    'adminEmail' => isset($conf['service']['mail']) ? $conf['service']['mail'] : 'admin@example.com',
    'service' => 'bedahsentral',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'last-operasi' => 'verifikasi-tagihan',
    'rs-type' => isset($conf['formula']['rs']) ? $conf['formula']['rs'] : 'mhkn',
	'frontend' => isset($conf['server']['url']) ? $conf['server']['url'] : '',
];
