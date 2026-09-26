<?php

/**
 * import_data_hasil_stok_opname
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    'import_data_cppt_pdf_rajal' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_cppt_pdf_rajal',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_cppt_pdf_rajal' => [
                'name' => 'cons_cppt_pdf_rajal',
                'callback' => \app\components\rabbitmq\CetakPdfCpptRajal::class,
                'routing_keys' =>  [
                    'cppt_pdf_rajal'
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
