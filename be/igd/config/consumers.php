<?php 

/**
 * import_data_instruksi_implementasi
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    // consumer import_data excel
    'import_data_instruksi_implementasi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_instruksi_implementasi',
            'type' => 'direct',
        ],
        'queues' => [
        ],
    ],
    'import_data_cppt_pdf' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_cppt_pdf',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_cppt_pdf_igd' => [
                'name' => 'cons_cppt_pdf_igd',
                'callback' => \app\components\rabbitmq\CpptPdfIgd::class,
                'routing_keys' =>  [
                    'cppt_pdf_igd'
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