<?php
namespace app\modules\v1\models;

use Yii;

class historyPemakaianObatOutFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "historypemakaianobat_fn";
    }

    
    public static function attributSchema()
    {
        return [
            'int4' => [
                'stokobatalkes_id',
                'ruangan_id',
                'obatalkes_id'
            ],
            'float8' => [
                'qtystok_out'
            ],
            'timestamp' => [
                'tanggal_transaksi'
            ],
            'date' => [
                'tglkadaluarsa'
            ],
            'varchar' => [
                'ruangan_nama',
                'obatalkes_kode',
                'no_transaksi',
                'obatalkes_nama',
                'satuanunit_nama',
                'reference'
            ],
            'text' => [
                'keterangan'
            ]
        ];
    }
}