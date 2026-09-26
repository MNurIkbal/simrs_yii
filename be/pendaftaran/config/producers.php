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
    'export_csv' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'export_csv',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_kamar_applicares' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_kamar_applicares',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_delete_kamar_applicares' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_delete_kamar_applicares',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'resend_task_jkn' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'resend_task_jkn',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_data_laporan_kunjungan_rawat_jalan' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rawat_jalan',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'resend_antrian_jkn' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'resend_antrian_jkn',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_data_laporan_kunjungan_rawat_darurat' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rawat_darurat',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_data_laporan_kunjungan_rumah_sakit' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data_laporan_kunjungan_rumah_sakit',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'bulk_register' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'bulk_register',
            'type' => 'direct'
        ],
        'queue_options' => [
            'declare' => false,
        ],
    ],
    'integrate_insurance' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'integrate_insurance',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'import_rencana_kontrol' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_rencana_kontrol',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
    'sync_rujukan_khusus' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'sync_rujukan_khusus',
            'type' => 'direct',
        ],
        'queue_options' => [
            'declare' => false, // Use this if you don't want to create a queue on producing messages
        ],
    ],
]);