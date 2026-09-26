<?php

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * sesuaikan juga di config console.php componen consumers
 *
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    'import_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false,
        ],
    ],
    'import_excel_lap_stock_inventory' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_excel_lap_stock_inventory',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false,
        ],
    ]
]);
