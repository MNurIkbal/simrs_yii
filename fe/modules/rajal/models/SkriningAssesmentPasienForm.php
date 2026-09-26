<?php

namespace app\modules\rajal\models;

use Yii;

class SkriningAssesmentPasienForm extends \yii\base\Model
{
    public $skrining_asesmen_rj_id;
    public $pendaftaran_id;
    public $alamat_pasien;
    public $kelurahan_desa;
    public $kecamatan;
    public $kabupaten;
    public $no_hp;
    public $tempat_lahir;
    public $tgl_lahir;
    public $jenis_kelamin;
    public $status_pernikahan;
    public $agama;
    public $pendidikan;
    public $pekerjaan;
    public $nama_ayah_atau_ibu;
    public $nama_ibu;
    public $pekerjaan_ayah_atau_ibu;
    public $nama_suami_atau_istri;
    public $pekerjaan_suami_atau_istri;
    public $cara_berkunjung;
    public $cara_bayar;
    public $no_peserta_bpjs_kis;
    public $status_kepesertaan;
    public $tgl_kunjungan;
    public $cara_masuk;
    public $petugas_loket_pendaftaran;
    public $pasien_keluarga_pasien;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['alamat_pasien', 'kelurahan_desa', 'kabupaten', 'kecamatan', 'no_hp','tempat_lahir','tgl_lahir','jenis_kelamin','status_pernikahan',
              'agama','pendidikan','pekerjaan','nama_ayah_atau_ibu','nama_ibu','pekerjaan_ayah_atau_ibu','nama_suami_atau_istri',
              'pekerjaan_suami_atau_istri','pendaftaran_id','cara_berkunjung','cara_bayar','no_peserta_bpjs_kis','status_kepesertaan','tgl_kunjungan',
              'cara_masuk','petugas_loket_pendaftaran','pasien_keluarga_pasien'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'alamat_pasien' => 'ALAMAT PASIEN',
            'kelurahan_desa' => 'KEL/DESA',
            'kecamatan' => 'KECAMATAN',
            'kabupaten' => 'KABUPATEN',
            'no_hp' => 'NO. TELEPON/HP',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tg. Lahir',
            'jenis_kelamin' => 'Sex',
            'status_pernikahan' => 'Status',
            'agama' => 'Agama',
            'pendidikan' => 'Pendidikan',
            'pekerjaan' => 'Pekerjaan',
            'nama_ayah_atau_ibu' => 'Nama Ayah/Ibu',
            'pekerjaan_ayah_atau_ibu' => 'Pekerjaan Ayah/Ibu',
            'nama_suami_atau_istri' => 'Nama Suami/Istri',
            'pekerjaan_suami_atau_istri' => 'Pekerjaan Suami/Istri',
            'cara_berkunjung' => 'Cara Berkunjung',
            'cara_bayar' => 'Cara Bayar',
            'no_peserta_bpjs_kis' => 'No. Peserta BPJS/KIS',
            'status_kepesertaan' => 'Status Kepesertaan',
            'tgl_kunjungan' => 'Tanggal / Jam Kunjungan',
            'cara_masuk' => 'Cara Masuk',
            'petugas_loket_pendaftaran' => 'Petugas Loket Pendaftaran',
            'pasien_keluarga_pasien' => 'Pasien / Keluarga Pasien'
        ];
    }
}
