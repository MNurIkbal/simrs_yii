<?php 

/**
 * import_data dan import_pdf 
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    'import_excel_inf_permintaan_makan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_excel_inf_permintaan_makan',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_import_excel_inf_permintaan_makan' => [
                'name' => 'cons_import_excel_inf_permintaan_makan',
                'callback' => \app\components\rabbitmq\InfPermintaanMakanExcel::class,
                'routing_keys' =>  [
                    'excel_inf_permintaan_makan'
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