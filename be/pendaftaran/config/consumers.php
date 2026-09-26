<?php 

/**
 * import_data dan import_pdf 
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    // consumer import_data excel
    'import_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sinkron_eklaim' => [
                'name' => 'cons_sinkron_eklaim',
                'callback' => \app\components\rabbitmq\Applicares::class,
                'routing_keys' =>  [
                    'sinkron_eklaim'
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
    'export_csv' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'export_csv',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_kunjungan_rawat_inap' => [
                'name' => 'cons_laporan_kunjungan_rawat_inap',
                'callback' => \app\components\rabbitmq\LaporanKunjunganRawatInapConsumer::class,
                'routing_keys' =>  [
                    'laporan_kunjungan_rawat_inap'
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
    'sync_kamar_applicares' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_kamar_applicares',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sync_kamar_applicares' => [
                'name' => 'cons_sync_kamar_applicares',
                'callback' => \app\components\rabbitmq\Applicares::class,
                'routing_keys' =>  [
                    'sync_applicares'
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
    'sync_delete_kamar_applicares' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_delete_kamar_applicares',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sync_delete_kamar_applicares' => [
                'name' => 'cons_ssync_delete_kamar_applicares',
                'callback' => \app\components\rabbitmq\Applicares::class,
                'routing_keys' =>  [
                    'sync_delete_applicares'
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
    'resend_task_jkn' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'resend_task_jkn',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_resend_task_jkn' => [
                'name' => 'cons_resend_task_jkn',
                'callback' => \app\components\rabbitmq\ResendTaskJkn::class,
                'routing_keys' =>  [
                    'resend_task_jkn',
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
    'import_data_laporan_kunjungan_rawat_jalan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rawat_jalan',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_kunjungan_rawat_jalan' => [
                'name' => 'cons_laporan_kunjungan_rawat_jalan',
                'callback' => \app\components\rabbitmq\LaporanKunjunganRawatJalanExcel::class,
                'routing_keys' =>  [
                    'laporan_kunjungan_rawat_jalan'
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
    'bulk_register' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'bulk_register',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_bulk_register' => [
                'name' => 'cons_bulk_register',
                'callback' => \app\components\rabbitmq\BulkRegisterReservasi::class,
                'routing_keys' =>  [
                    'bulk_register_reservasi',
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
    'resend_antrian_jkn' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'resend_antrian_jkn',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_resend_antrian_jkn' => [
                'name' => 'cons_resend_antrian_jkn',
                'callback' => \app\components\rabbitmq\ResendAntrianJknConsumer::class,
                'routing_keys' =>  [
                    'resend_antrian_jkn'
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
    'import_data_laporan_kunjungan_rawat_darurat' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rawat_darurat',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_kunjungan_rawat_darurat' => [
                'name' => 'cons_laporan_kunjungan_rawat_darurat',
                'callback' => \app\components\rabbitmq\LaporanKunjunganRawatDaruratExcel::class,
                'routing_keys' =>  [
                    'laporan_kunjungan_rawat_darurat'
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
    'import_data_laporan_kunjungan_rumah_sakit' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rumah_sakit',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_kunjungan_rumah_sakit' => [
                'name' => 'cons_laporan_kunjungan_rumah_sakit',
                'callback' => \app\components\rabbitmq\LaporanKunjunganRumahSakitExcel::class,
                'routing_keys' =>  [
                    'laporan_kunjungan_rumah_sakit'
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
    'integrate_insurance' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'integrate_insurance',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_integrate_insurance' => [
                'name' => 'cons_integrate_insurance',
                'callback' => \app\components\rabbitmq\IntegrateInsurance::class,
                'routing_keys' =>  [
                    'integrate_insurance'
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
    'import_rencana_kontrol' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_rencana_kontrol',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_import_rencana_kontrol' => [
                'name' => 'cons_import_rencana_kontrol',
                'callback' => \app\components\rabbitmq\InfRencanaKontrolConsumer::class,
                'routing_keys' =>  [
                    'import_rencana_kontrol'
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
    'sync_rujukan_khusus' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_rujukan_khusus',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sync_rujukan_khusus' => [
                'name' => 'cons_sync_rujukan_khusus',
                'callback' => \app\components\rabbitmq\SyncRujukanKhususConsumer::class,
                'routing_keys' =>  [
                    'sync_rujukan_khusus'
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
]);