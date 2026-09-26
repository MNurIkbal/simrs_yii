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
            'cons_laporan_klasifikasi_kamar' => [
                'name' => 'cons_laporan_klasifikasi_kamar',
                'callback' => \app\components\rabbitmq\KlasifikasiKamarConsumer::class,
                'routing_keys' =>  [
                    'laporan_klasifikasi_kamar'
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
    'extract_csv' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'extract_csv',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_extract_csv' => [
                'name' => 'cons_extract_csv',
                'callback' => \app\components\rabbitmq\ExtractCsvConsumer::class,
                'routing_keys' =>  [
                    'extract_csv'
                ], // Queue will be binded using routing key
                // Other optional settings can be listed here (like in queue_options)
                'durable' => true,
                'auto_delete' => false
            ],
        ],
    ],
]);