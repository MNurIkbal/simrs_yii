<?php
namespace app\modules\v1\models;

use Yii;

class InfoStokObatAlkesAllFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "infostokobatalkesall_fn";
    }

    public function getInfoStokObatAlkesFn($penjamin_id, $kelaspelayanan_id = 0) {
        return (new InfoStokObatAlkesAllFn(['extParam'=>[$penjamin_id,$kelaspelayanan_id]]))->find()->select([
                    'instalasi_id',
                    'obatalkes_id',
                    'obatalkes_kode',
                    'obatalkes_namalain',
                    'obatalkes_nama',
                    'qty_tersedia',
                    'ppn',
                    'hargaygdipakai as hargajual',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'satuansedang_id',
                    'satuansedang_nama',
                    'satuanbesar_id',
                    'satuanbesar_nama',
                    'harganetto_ygdipakai as harganetto',
                    'ruangan_id',
                    'instalasi_id',
                    'hargaygdipakai',
                    'hn_diskon',
                    'hn_ppn',
                    'hn_margin',
                    'disc',
                    'ppn',
                    'margin',
                    'group_jenisobat',
                    'group_jenisobat_nama',
                    'jenisobatalkes_id',
                    'jenisobatalkes_nama']);
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'xpenjamin_id',
                'xkelaspelayan_id',
                'xjenisobatalkes_id',
                'periodestok_id',
                'obatalkes_id',
                'satuanbesar_id',
                'satuankecil_id',
                'satuansedang_id',
                'group_jenisobat',
                'jenisobatalkes_id',
                'persenmargin_id'
            ],
            'varchar' => [
                'periodestok_nama',
                'obatalkes_nama',
                'obatalkes_namalain',
                'obatalkes_kode',
                'satuanbesar_nama',
                'satuankecil_nama',
                'satuansedang_nama',
                'group_jenisobat_nama',
                'jenisobatalkes_nama'
            ],
            'float8'  => [
                'qty_masuk',
                'qty_keluar',
                'qty_dipesan',
                'qty_tersedia',
                'qty_stok',
                'nilai_ro',
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
            ],
            'timestamp' => [
                'tglperiodestok_awal',
                'tglperiodestok_akhir'

            ]
        ];
    }
}