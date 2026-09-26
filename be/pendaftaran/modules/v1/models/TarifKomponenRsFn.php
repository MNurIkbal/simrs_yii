<?php
namespace app\modules\v1\models;

use Yii;

class TarifKomponenRsFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return 'tarifkomponenrs_fn';
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'tariftindakan_id',
                'ruangan_id',
                'instalasi_id',
                'ruanganpaket_id',
                'perdatarif_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'kelompoktindakan_id',
                'kategoritindakan_id',
                'daftartindakan_id',
                'tipepaket_id',
                'komponentarif_id',
                'carabayar_id',
                'kamarruangan_id',
                'ambulan_id',
                'kelompokpemeriksaanlab_id',
                'jenispemeriksaanlab_id',
                'pemeriksaanlab_id',
            ],
            'varchar' => [
                'instalasi_nama',
                'ruangan_nama',
                'ruanganpaket_nama',
                'perdanama_sk',
                'kelaspelayanan_nama',
                'penjamin_nama',
                'kelompoktindakan_nama',
                'kategoritindakan_nama',
                'daftartindakan_nama',
                'komponentarif_nama',
                'kamarruangan_nokamar',
                'tipepaket_nama',
                'no_polisi',
                'nama_kelompok',
                'jenispemeriksaanlab_nama',
                'pemeriksaanlab_nama',
            ],
            'float8'  => [
                'harga_tariftindakan',
                'persencyto_tindakan',
                'persendiskon_tindakan',
                'persen_penyulit'
            ],
            'text' => [
                'jenis',
            ],
            'bool' => [
                'is_default',
                'is_akomodasi',
                'is_konsultasi',
            ]
        ];
    }
}
