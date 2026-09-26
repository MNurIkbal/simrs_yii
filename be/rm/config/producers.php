<?php 

require_once 'initConsumers.php';
// use app\config\initConsumers;
/**
 * import_data_sensus_harian_rajal
 * sesuaikan juga di config console.php componen consumers
 * 
 * procedurs ini yang nantinya dipanggil di BE,sesuai dengan nama consumersnya 
 */
return (new initConsumers())->transform([
    /** producers import data excel */
    'import_data_sensus_harian_rajal' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_sensus_harian_rajal',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_data_pendapatan_ruangan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_pendapatan_ruangan',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_data_laporan_kunjungan_pasien_rs' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_pasien_rs',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
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
    'esign_dokumen_proses' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'esign_dokumen_proses',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'tilaka_status' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'tilaka_status',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);