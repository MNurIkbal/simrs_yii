<?php

namespace app\modules\v1\models;

use Yii;

class LaporanPasienLabKasirView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanpasienlabkasir_v';
    }

    public function rules()
    {
        return [
            [['tipe_pasien', 'no_rujukan', 'asalrujukan_nama', 'ruangan_nama', 'harga'], 'string'],
            [['pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'pegawai_id', 'asalrujukan_id', 'ruanganasal_id', 'ruangan_id'], 'default', 'value' => null],
            [['pasienmasukpenunjang_id', 'pasienkirimkeunitlain_id', 'pegawai_id', 'asalrujukan_id', 'ruanganasal_id', 'ruangan_id'], 'integer'],
            [['tglmasukpenunjang', 'harga'], 'safe'],
            [['no_pendaftaran', 'no_masukpenunjang'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'dokter_penunjang', 'status_periksa'], 'string', 'max' => 50],
        ];
    }

     /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tipe_pasien' => 'Tipe Pasien',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'tglmasukpenunjang' => 'Tglmasukpenunjang',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_masukpenunjang' => 'No Masukpenunjang',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'pegawai_id' => 'Pegawai ID',
            'dokter_penunjang' => 'Dokter Penunjang',
            'no_rujukan' => 'No Rujukan',
            'asalrujukan_id' => 'Asalrujukan ID',
            'asalrujukan_nama' => 'Asalrujukan Nama',
            'ruanganasal_id' => 'Ruanganasal ID',
            'ruangan_nama' => 'Ruangan Nama',
            'status_periksa' => 'Status Periksa',
            'harga' => 'Harga',
            'ruangan_id' => 'Ruangan ID',
        ];
    }

}
