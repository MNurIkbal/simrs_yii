<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Doco\components\DocoPostgreFunctionAR;

class KartuStokObatFn extends DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "kartustokobat_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
            	'stokobatalkes_id',
            	'ruangan_id',
            	'ruangan_asal_id',
            	'ruangan_tujuan_id',
            	'obatalkes_id'
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
            	'obatalkes_kode',
            	'no_transaksi',
            	'obatalkes_nama',
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