<?php

/**
 * Init Consumer
 * sesuaikan juga di config web.php componen producers
 */
require_once 'initConsumers.php';
use PhpAmqpLib\Wire\AMQPTable;

return (new initConsumers())->transform([
    'import_data' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_data',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_laporan_mutasi_obat_alkes_excel' => [
                'name' => 'cons_laporan_mutasi_obat_alkes_excel',
                'callback' => \app\components\rabbitmq\LaporanMutasiObatAlkesExcelConsumer::class,
                'routing_keys' =>  [
                    'laporan_mutasi_obat_alkes_excel'
                ],
                'durable' => true,
                'auto_delete' => false,
                'arguments' => new AMQPTable([
                    'x-expire' => 600000,
                    'x-message-ttl' => 600000,
                ])
            ],
        ],
    ],
    'import_excel_lap_stock_inventory' => [
        'connection' => 'default',
        'exchange_options' => [
            'name' => 'import_excel_lap_stock_inventory',
            'type' => 'direct',
        ],
        'queues' => [
            'cons_import_excel_lap_stock_inventory' => [
                'name' => 'cons_import_excel_lap_stock_inventory',
                'callback' => \app\components\rabbitmq\LapStockInventoryExcel::class,
                'routing_keys' =>  [
                    'excel_lap_stock_inventory'
                ],
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
