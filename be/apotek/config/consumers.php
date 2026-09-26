<?php 

/**
 * import_data_hasil_stok_opname
 * import_data dan import_pdf 
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    // consumer import_data excel
    'import_data_hasil_stok_opname' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_hasil_stok_opname',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_hasil_stok_opname' => [
                'name' => 'cons_laporan_hasil_stok_opname',
                'callback' => \app\components\rabbitmq\LaporanHasilStokOpname::class,
                'routing_keys' =>  [
                    'laporan_hasil_stok_opname'
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
    
    'import_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_penjualan_resep' => [
                'name' => 'cons_laporan_penjualan_resep',
                'callback' => \app\components\rabbitmq\LaporanPenjualanResepConsumer::class,
                'routing_keys' =>  [
                    'laporan_penjualan_resep'
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

    'push_notif_farmasi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'push_notif_farmasi',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_trigger_push_notif_farmasi' => [
                'name' => 'cons_trigger_push_notif_farmasi',
                'callback' => \app\components\rabbitmq\PushNotification::class,
                'routing_keys' =>  [
                    'trigger_push_notif_farmasi',
                    'trigger_update_notif_farmasi'
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

    'import_data_laporan_rekapitulasi_penjualan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_rekapitulasi_penjualan',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_rekapitulasi_penjualan' => [
                'name' => 'cons_laporan_rekapitulasi_penjualan',
                'callback' => \app\components\rabbitmq\LaporanRekapitulasiPenjualanConsumer::class,
                'routing_keys' =>  [
                    'laporan_rekapitulasi_penjualan',
                ],
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