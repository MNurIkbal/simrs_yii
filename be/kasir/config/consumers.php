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
            'cons_laporan_kasir' => [
                'name' => 'cons_laporan_kasir',
                'callback' => \app\components\rabbitmq\KasirLaporan::class,
                'routing_keys' =>  [
                    'laporan_kasir'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
            'cons_laporan_cara_bayar_kasir' => [
                'name' => 'cons_laporan_cara_bayar_kasir',
                'callback' => \app\components\rabbitmq\KasirLaporan::class,
                'routing_keys' =>  [
                    'laporan_cara_bayar_kasir'
                ],
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'sync_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_data',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_integrasi_eklaim' => [
                'name' => 'cons_integrasi_eklaim',
                'callback' => \app\components\rabbitmq\IntegrasiEklaim::class,
                'routing_keys' =>  [
                    'integrasi_eklaim'
                ], 
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