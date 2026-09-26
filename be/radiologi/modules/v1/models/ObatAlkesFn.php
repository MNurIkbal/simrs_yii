<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkes_f".
 *
 * @property int $obatalkes_id
 */
class ObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * {@inheritdoc}
     */
    
    public static function functionName()
    {
        return 'infostokobatalkes_fn';
    }


    public static function attributSchema()
    {
        return [
                'int4' => ['obatalkes_id', 
                            'grup_jenisobat_id',
                            'periodestok_id',
                            'instalasi_id',
                            'ruangan_id',
                            'qty_masuk',
                            'qty_keluar',
                            'qty_dipesan',
                            'qty_tersedia',
                            'qty_sisa',
                            'nilai_ro',
                            'jenisobatalkes_id', 
                            'satuankecil_id', 
                            'satuansedang_id', 
                            'satuanbesar_id', 
                            'groupinacbg_id'
                        ],
                'varchar' => ['obatalkes_nama', 
                            'obatalkes_namalain', 
                            'obatalkes_kode',
                            'grup_jenisobat',
                            'periodestok_nama',
                            'instalasi_nama',
                            'ruangan_nama',
                            'jenisobatalkes_nama', 
                            'satuankecil', 
                            'satuansedang', 
                            'satuanbesar'],
                'text' => ['konfigygdigunakan'],
                'float8' => ['persen_ppn', 
                            'persen_disc', 
                            'persen_margin', 
                            'jml_hargajual', 
                            'jml_harganetto', 
                            'jml_margin', 
                            'jml_discount', 
                            'jml_ppn'],
            ];
    }
}
