
<?php 

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * import_data_cppt_pdf_rajal
 * sesuaikan juga di config console.php componen consumers
 * 
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    /** producers import data excel */
    'import_data_cppt_pdf_rajal' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_cppt_pdf_rajal',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);