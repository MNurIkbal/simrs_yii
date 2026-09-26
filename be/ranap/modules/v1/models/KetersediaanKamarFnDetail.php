<?php

namespace app\modules\v1\models;

use Yii;

class KetersediaanKamarFnDetail extends \Doco\components\DocoPostgreFunctionAR
{
    public static function functionName()
    {
        return "fgetketersediaankamar_detail";
    }

    public static function attributSchema()
    {
        return [
            'int4' => [
                'pendaftaran_id',
                'jeniskelamin_id',
                'dokter_id',
                'kamartempattidur_id',
                'kamarruangan_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'kettempattidur_id',
            ],
            'varchar' => [
                'no_rekam_medik',
                'nama_pasien',
                'jeniskelamin_nama',
                'umur',
                'dokter_nama',
                'ruangan_sebelum_nama',
                'is_stopakomodasi',
                'kamarruangan_nokamar',
                'ruangan_nama',
                'kamarruangan_jenis',
                'kamarruangan_jenis_nama',
                'no_tempattidur',
                'kelaspelayanan_nama',
                'status_isi',
                'kode_warna',
                'kettempattidur_warna',
                'additional_data',

            ],
            'float8'  => [
                'harga_tariftindakan',
                'total_isi',
                'total_kosong'
            ],
            'date' => [
                'tanggal_lahir',
                'tanggal_masuk',
            ],
        ];
    }
}
?>