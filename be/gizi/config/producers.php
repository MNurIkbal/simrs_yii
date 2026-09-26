<?php 

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * import_data dan import_pdf 
 * sesuaikan juga di config console.php componen consumers
 * 
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    /** producers import data excel */
    'import_excel_inf_permintaan_makan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_excel_inf_permintaan_makan',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ]
]);