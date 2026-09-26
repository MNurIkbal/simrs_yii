<?php 

/**
 * import_data_sensus_harian_rajal
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    // consumer import_data excel
    'import_data_sensus_harian_rajal' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_sensus_harian_rajal',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_sensus_harian_rajal' => [
                'name' => 'cons_laporan_sensus_harian_rajal',
                'callback' => \app\components\rabbitmq\LaporanSensusHarianRajal::class,
                'routing_keys' =>  [
                    'laporan_sensus_harian_rajal'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
            [
                'name' => 'cons_laporan_sensus_harian_ranap',
                'callback' => \app\components\rabbitmq\LaporanSensusHarianRanap::class,
                'routing_keys' =>  [
                    'laporan_sensus_harian_ranap'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'import_data_pendapatan_ruangan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_pendapatan_ruangan',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_pendapatan_ruangan' => [
                'name' => 'cons_laporan_pendapatan_ruangan',
                'callback' => \app\components\rabbitmq\LaporanPendapatanRuangan::class,
                'routing_keys' =>  [
                    'laporan_pendapatan_ruangan'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'import_data_laporan_kunjungan_pasien_rs' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_pasien_rs',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_kunjungan_pasien_rs' => [
                'name' => 'cons_laporan_kunjungan_pasien_rs',
                'callback' => \app\components\rabbitmq\LaporanKunjunganPasienRs::class,
                'routing_keys' =>  [
                    'laporan_kunjungan_pasien_rs'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'esign_dokumen_proses' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'esign_dokumen_proses',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_esign' => [
                'name' => 'cons_esign',
                'callback' => \app\components\rabbitmq\Esign::class,
                'routing_keys' =>  [
                    'esign_generate',
                    'tilaka_check',
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'tilaka_status' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'tilaka_status',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_tilaka_status' => [
                'name' => 'cons_tilaka_status',
                'callback' => \app\components\rabbitmq\Esign::class,
                'routing_keys' =>  [
                    'esign_reg_status',
                    'esign_cert_status',
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ]
]);