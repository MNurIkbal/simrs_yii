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
                'callback' => \app\components\rabbitmq\SinkronEklaim::class,
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
    'sync_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_data',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_trigger_sinkron_eklaim' => [
                'name' => 'cons_trigger_sinkron_eklaim',
                'callback' => \app\components\rabbitmq\TriggerEklaim::class,
                'routing_keys' =>  [
                    'trigger_sinkron_eklaim'
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
    'sync_dokumen_eklaim' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_dokumen_eklaim',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sinkron_dokumen_eklaim' => [
                'name' => 'cons_sinkron_dokumen_eklaim',
                'callback' => \app\components\rabbitmq\DokumenEklaim::class,
                'routing_keys' =>  [
                    'integrasi_dokumen_eklaim'
                ],
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                ])
            ],
        ],
    ],
    'sync_integrasi_asuransi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_integrasi_asuransi',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sync_integrasi_asuransi' => [
                'name' => 'cons_sync_integrasi_asuransi',
                'callback' => \app\components\rabbitmq\IntegrasiAsuransi::class,
                'routing_keys' =>  [
                    'sync_integrasi_asuransi_data'
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
    'sync_dokumen_resep_kronis' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_dokumen_resep_kronis',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_sync_dokumen_resep_kronis' => [
                'name' => 'cons_sync_dokumen_resep_kronis',
                'callback' => \app\components\rabbitmq\DokumenResepKronis::class,
                'routing_keys' =>  [
                    'dokumen_resep_kronis'
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