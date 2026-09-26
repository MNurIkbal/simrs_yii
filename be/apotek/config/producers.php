<?php 

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * import_data_hasil_stok_opname
 * import_data dan import_pdf 
 * sesuaikan juga di config console.php componen consumers
 * 
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    /** producers import data excel */
    'import_data_hasil_stok_opname' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_hasil_stok_opname',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    
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

    'import_data_laporan_rekapitulasi_penjualan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_rekapitulasi_penjualan',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],

    'push_notif_farmasi' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'push_notif_farmasi',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);
