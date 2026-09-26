<?php 
namespace app\modules\v1\models;

use Yii;

class PemberianPiutangFn extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "pemberianpiutang_fn";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'pendaftaran_id',
                'penjualanresep_id',
                'satuanpembulatan',
                'tarif_max',
            ],
            'varchar' => [
                'no_pendaftaran',
                'no_resep',
                'no_rekam_medik',
                'nama_pasien',
                'umur',
            ],
            'float8'  => [
                'total_tagihan',
                'piutang_sudahbayar',
                'total_piutang',
                'tagihan_ranap',
                'uang_muka',
            ],
            'float4' => [
                'adm_persen',
            ],
            'text' => [
                'jenis',
                'keywords',
            ],
            'bool' => [
                'is_pembulatankeatas',
                'is_batal',
            ],
            'date' => [
                'tanggal_lahir',
                'tgl_pendaftaran',
                'created_date',
            ],
        ];
    }
}
