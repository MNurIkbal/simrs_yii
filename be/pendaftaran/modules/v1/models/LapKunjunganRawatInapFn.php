<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kunjungan rawat inap".
 *
 */
class LapKunjunganRawatInapFn extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * @inheritdoc
     */
    public static function functionName()
    {
        return 'laporankunjunganri_fn';
    }

    public static function attributSchema()
    {
        return [
            'date' => [
                'tgl_pendaftaran',
                'tgl_keluar'
            ],
            'varchar' => [
                'no_pendaftaran',
                'no_rekam_medik',
                'nama_pasien',
                'jenis_kelamin',
                'umur',
                'golonganumur_nama',
                'agama',
                'statusperkawinan',
                'pekerjaan_nama',
                'carabayar_nama',
                'penjamin_nama',
                'nama_perujuk',
                'ruangan_nama',
                'kamarruangan_nokamar',
                'no_tempattidur',
                'nama_pegawai',
                'kelaspelayanan_nama',
                'kelas_ditagihkan_nama',
                'status_ranap_nama',
                'carakeluar_nama',
                'kabupaten_nama',
                'kunjungan',
                'jeniskasuspenyakit_nama',
                'nosep',
                'status_pasien'
            ],
            'text' => [
                'alamat_pasien',
                'diagnosa'
            ],
            'integer' => [
                'golonganumur_id',
                'carabayar_id',
                'penjamin_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'ruangan_id',
                'pegawai_id'
            ],
            'boolean' => [
                'is_pasientitipan',
                'is_pasientitipan_pk'
            ]
        ];
    }

    public static function getData($date_start = null, $date_end = null)
    {
        $date_start = !empty($date_start) ? date('Y-m-d 00:00:00',strtotime($date_start)) : date('Y-m-d 00:00:00');
        $date_end = !empty($date_end) ? date('Y-m-d 23:59:59',strtotime($date_end)) : date('Y-m-d 23:59:59');
        return (new LapKunjunganRawatInapFn(['extParam'=>[$date_start, $date_end]]))->find();
    }
}
