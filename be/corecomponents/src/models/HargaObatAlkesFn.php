<?php

namespace Doco\models;

use Yii;

class HargaObatAlkesFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "hargaobatalkes_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'xpenjamin_id',
                'xkelaspelayan_id',
                'obatalkes_id',
                'satuanbesar_id',
                'satuankecil_id',
                'satuansedang_id',
                'jenisobatalkes_id',
                'persenmargin_id'
            ],
            'varchar' => [
                'obatalkes_nama',
                'obatalkes_namalain',
                'obatalkes_kode',
                'satuanbesar_nama',
                'satuankecil_nama',
                'satuansedang_nama',
                'jenisobatalkes_nama'
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
                'hargaygdipakai',
                'harganetto_ygdipakai',
                'harganetto_sugesstion',
                'hn_margin',
                'hn_diskon',
                'hn_ppn',
                'persen_ppn',
                'persen_margin',
                'persen_disc',
                'jml_hargajual',
                'jml_harganetto',
                'jml_sugesstion',
                'jml_margin',
                'jml_discount',
                'jml_ppn',
                'embalase_racikan',
                'embalase_nonracikan'
            ],
            'text' => [
                'konfigygdigunakan'
            ]
        ];
    }
}