<?php
$ini = @parse_ini_file('env/.env', true);

return (object) [
    'adminEmail' => 'admin@example.com',
    'bsVersion' => '3.x',
    'mode' => isset($conf['redis']['name']) ? $conf['redis']['name'] : 'dev',
    'ownerApps' => 'bdg-simrs-doco-development',
    'bsDependencyEnabled' => true,
    'pjaxDuplicationFix' => true,
    'thirdApp' => isset($ini['thirdapp']) ? $ini['thirdapp'] : [],
    'rajalTabs' =>  isset($ini['rajaltabs']) ? $ini['rajaltabs'] : [],
    'ranapTabs' =>  isset($ini['ranaptabs']) ? $ini['ranaptabs'] : [],
    'igdTabs' =>  isset($ini['igdtabs']) ? $ini['igdtabs'] : [],
    'iCare' => isset($ini['icare']) ? $ini['icare'] : [],
    'logView' => [
        'backend' => [
            'dcms' => [
                'name' => 'DCMS',
                'folder' => 'dcms',
                'icon' => '/media/img/newsimrs_image/logo-menu/01 SYSADMIN.svg'
            ],
            'ambulan' => [
                'name' => 'Ambulan',
                'folder' => 'ambulan',
                'icon' => '/media/img/newsimrs_image/logo-menu/18 MODUL AMBULAN.svg'
            ],
            'bedah' => [
                'name' => 'Bedah Sentral',
                'folder' => 'bedah',
                'icon' => '/media/img/newsimrs_image/logo-menu/11 MODUL BEDAH SENTRAL.svg'
            ],
            'radiologi' => [
                'name' => 'Radiologi',
                'folder' => 'radiologi',
                'icon' => '/media/img/newsimrs_image/logo-menu/08 MODUL RADIOLOGI.svg'
            ],
            'laboratorium' => [
                'name' => 'Laboratorium',
                'folder' => 'laboratorium',
                'icon' => '/media/img/newsimrs_image/logo-menu/07 MODUL LABORATORIUM.svg'
            ],
            'pendaftaran' => [
                'name' => 'Pendaftaran',
                'folder' => 'pendaftaran',
                'icon' => '/media/img/newsimrs_image/logo-menu/13 MODUL PENDAFTARAN.svg'
            ],
            'ranap' => [
                'name' => 'Ranap',
                'folder' => 'ranap',
                'icon' => '/media/img/newsimrs_image/logo-menu/10 MODUL RAWAT INAP.svg'
            ],
            'rajal' => [
                'name' => 'Rawat Jalan',
                'folder' => 'rajal',
                'icon' => '/media/img/newsimrs_image/logo-menu/05 MODUL RAWAT JALAN.svg'
            ],
            'apotek' => [
                'name' => 'Farmasi',
                'folder' => 'apotek',
                'icon' => '/media/img/newsimrs_image/logo-menu/03 APOTEK.svg'
            ],
            'gudang' => [
                'name' => 'Gudang',
                'folder' => 'gudang',
                'icon' => '/media/img/newsimrs_image/logo-menu/02 GUDANG FARMASI.svg'
            ],
            'pengadaan' => [
                'name' => 'Pengadaan',
                'folder' => 'pengadaan',
                'icon' => '/media/img/newsimrs_image/logo-menu/16 MODUL PENGADAAN.svg'
            ],
            'rm' => [
                'name' => 'Rekam Medik',
                'folder' => 'rm',
                'icon' => '/media/img/newsimrs_image/logo-menu/06 MODUL REKAM MEDIK.svg'
            ],
            'gizi' => [
                'name' => 'Gizi',
                'folder' => 'gizi',
                'icon' => '/media/img/newsimrs_image/logo-menu/22 MODUL GIZI.svg'
            ],
            'igd' => [
                'name' => 'Rawat Darurat',
                'folder' => 'igd',
                'icon' => '/media/img/newsimrs_image/logo-menu/09 MODUL RAWAT DARURAT.svg'
            ],
        ],
        'frontend' => [
            'name' => 'Frontend',
            'folder' => 'fe',
            'icon' => '/media/img/newsimrs_image/logo-menu/11 MODUL BEDAH SENTRAL.svg'
        ]
    ],
    'import' => [
        'enabled' => isset($ini['import']['enabled']) ? (bool) $ini['import']['enabled'] :false,
        'address' => isset($ini['import']['address']) ? $ini['import']['address'] :null,
        'key' => isset($ini['import']['key']) ? $ini['import']['key'] :null,
    ],
    'goexcelexporter' => [
        'uri_api' => isset($ini['goexcelexporter']['url']) ? $ini['goexcelexporter']['url'] : 'http://goexcelexporter:4646',
    ],
];