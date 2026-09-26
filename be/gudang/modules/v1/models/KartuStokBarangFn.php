<?php

namespace app\modules\v1\models;

class KartuStokBarangFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "kartustokbarang_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
            	'stokbarang_id',
            	'ruangan_id',
            	'ruangan_asal_id',
            	'ruangan_tujuan_id',
            	'barang_id'
            ],
            'int8' => [
            	'row_number'
            ],
            'float8' => [
            	'qtystok_in',
            	'qtystok_out',
            	'total'
            ],
            'varchar' => [
            	'ruangan_asal_nama',
            	'ruangan_tujuan_nama',
            	'barang_kode',
            	'no_transaksi',
            	'barang_nama',
            	'satuanunit_nama',
            	'reference'
            ],
            'text' => [
            	'keterangan'
            ],
            'date' => [
            	'tglkadaluarsa'
            ],
            'timestamp' => [
            	'tanggal_transaksi'
            ]
        ];
    }
}