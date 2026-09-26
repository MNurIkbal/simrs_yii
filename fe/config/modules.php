<?php

return [
      'master' => [
         'class' => Doco\master\Module::class,
      ],
      'informasi' => [
         'class' => Doco\informasi\Module::class,
      ],
      'laporan' => [
         'class' => Doco\laporan\Module::class,
      ],
      'rm' => [
         'class' => Doco\rm\Module::class,
      ],
      'dcms' => [
         'class' => Doco\dcms\Module::class,
      ],
      'rajal' => [
         'class' => Doco\rajal\Module::class,
      ],
      'antrian' => [
         'class' => Doco\antrian\Module::class,
         'defaultRoute' => 'dashboard'
      ],
      'apotek' => [
         'class' => Doco\apotek\Module::class,
      ],
      'kasir' => [
         'class' => Doco\kasir\Module::class,
      ],
      'pendaftaran' => [
         'class' => Doco\pendaftaran\Module::class,
      ],
      'api' => [
         'class' => Doco\api\Module::class,
      ],
      'gudang' => [
         'class' => Doco\gudang\Module::class,
      ],
      'ranap' => [
         'class' => Doco\ranap\Module::class,
      ],
      'radiologi' => [
         'class' => Doco\radiologi\Module::class,
      ],
      'laboratorium' => [
         'class' => Doco\laboratorium\Module::class,
      ],
      'bedah' => [
         'class' => Doco\bedah\Module::class,
      ],
      'sysadmin' => [
         'class' => Doco\master\Module::class,
      ],
      'igd' => [
         'class' => Doco\igd\Module::class,
      ],
      'penjamin-asuransi' => [
         'class' => Doco\penjaminasuransi\Module::class,
      ],
      'pengadaan' => [
         'class' => Doco\pengadaan\Module::class,
      ],
      'gizi' => [
         'class' => Doco\gizi\Module::class,
      ],
      'jenazah' => [
         'class' => Doco\jenazah\Module::class,
      ],
      'ambulan' => [
         'class' => Doco\ambulan\Module::class,
      ],
      'bankdarah' => [
         'class' => Doco\bankdarah\Module::class,
      ],
      'penatajasa' => [
         'class' => Doco\penatajasa\Module::class,
      ],
      'mcu' => [
         'class' => Doco\mcu\Module::class,
      ],
      'fisioterapi' => [
         'class' => Doco\fisioterapi\Module::class,
      ],
      'cssd' => [
         'class' => Doco\cssd\Module::class,
      ],
      'anestesi' => [
         'class' => Doco\bedah\Module::class,
      ],
      'remunerasi' => [
         'class' => Doco\remunerasi\Module::class,
         'defaultRoute' => 'integrasi',
      ],
];
