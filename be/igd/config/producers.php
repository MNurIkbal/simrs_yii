<?php 

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * import_data_instruksi_implementasi
 * sesuaikan juga di config console.php componen consumers
 * 
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    /** producers import data pdf */
    'import_data_instruksi_implementasi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_instruksi_implementasi',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    /** producers import data pdf */
    'import_data_cppt_pdf' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_cppt_pdf',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);
