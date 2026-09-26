<?php 
namespace app\modules\v1\models;

use Yii;

class ObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "infostokobatalkes_fn";
    }
    public static function attributSchema()
    {
        return [
            'int4' => [
                'obatalkes_id',
                'jenisobatalkes_id',
                'satuankecil_id',
                'satuansedang_id',
                'satuanbesar_id',
                'groupinacbg_id',
                'periodestok_id',
                'instalasi_id',
                'ruangan_id',
                'qty_masuk',
                'qty_keluar',
                'qty_dipesan',
                'qty_tersedia',
                'group_jenisobat_id',
            ],
            'int8' => [
                'qty_stok',
                'nilai_ro'
            ],
            'varchar' => [
                'obatalkes_nama',
                'jenisobatalkes_nama',
                'satuankecil',
                'satuansedang',
                'satuanbesar'
            ],
            'float8'  => [
                'hargajual',
                'ppn',
                'margin',
                'disc',
                'harganetto',
                'hn_last_margin',
                'hn_last_diskon',
                'hn_last_margin_diskon',
                'hn_last_ppn',
                'hargajual_last',
                'hargamaksimum',
                'hn_max_margin',
                'hn_max_diskon',
                'hn_max_margin_diskon',
                'hn_max_ppn',
                'hargajual_max',
                'hargaminimum',
                'hn_min_margin',
                'hn_min_diskon',
                'hn_min_margin_diskon',
                'hn_min_ppn',
                'hargajual_min',
                'hargaratarata',
                'hn_avg_margin',
                'hn_avg_diskon',
                'hn_avg_margin_diskon',
                'hn_avg_ppn',
                'hargajual_avg',
                'persen_ppn',
                'persen_margin',
                'persen_disc',
                'hargaygdipakai',
                'harganetto_ygdipakai',
                'hn_margin',
                'hn_diskon',
                'hn_ppn',
                'jml_hargajual',
                'jml_harganetto',
                'jml_margin',
                'jml_discount',
                'jml_ppn',
            ],
            'text' => [
                'konfigygdigunakan'
            ]
        ];
    }
}