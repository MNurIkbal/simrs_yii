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
    'import_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    /** producers import data pdf */
    'import_pdf' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_pdf',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    /** producers import data pdf */
    'sync_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_data',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_dokumen_eklaim' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_dokumen_eklaim',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_integrasi_asuransi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_integrasi_asuransi',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_dokumen_resep_kronis' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_dokumen_resep_kronis',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);