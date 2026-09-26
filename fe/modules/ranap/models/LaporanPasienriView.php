<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "laporanpasienri_v".
 *
 * @property int $pendaftaran_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $pasien_id
 * @property int $jeniskasuspenyakit_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $Tanggal Masuk
 * @property string $Tanggal Keluar
 * @property string $No. Rekam Medik
 * @property string $No. Pendaftaran
 * @property string $Nama Pasien
 * @property string $Jenis Kelamin
 * @property string $Dokter
 * @property string $Cara Bayar
 * @property string $Penjamin
 * @property string $Kelas Pelayanan
 * @property string $Jenis Kasus Penyakit
 * @property string $Ruangan
 * @property int $Lama Rawat
 * @property string $alasan_batal
 * @property int $status_ranap
 * @property string $status_ranap_nama
 */
class LaporanPasienriView extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'pegawai_id', 'Lama Rawat', 'status_ranap'], 'default', 'value' => null],
            [['pendaftaran_id', 'carabayar_id', 'penjamin_id', 'pasien_id', 'jeniskasuspenyakit_id', 'ruangan_id', 'pegawai_id', 'Lama Rawat', 'status_ranap'], 'integer'],
            [['Tanggal Masuk', 'Tanggal Keluar'], 'safe'],
            [['alasan_batal'], 'string'],
            [['No. Rekam Medik'], 'string', 'max' => 10],
            [['No. Pendaftaran'], 'string', 'max' => 20],
            [['Nama Pasien', 'Dokter', 'Cara Bayar', 'Penjamin', 'Kelas Pelayanan', 'Ruangan'], 'string', 'max' => 50],
            [['Jenis Kelamin', 'status_ranap_nama'], 'string', 'max' => 200],
            [['Jenis Kasus Penyakit'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'pasien_id' => 'Pasien ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'Tanggal Masuk' => 'Tanggal  Masuk',
            'Tanggal Keluar' => 'Tanggal  Keluar',
            'No. Rekam Medik' => 'No   Rekam  Medik',
            'No. Pendaftaran' => 'No   Pendaftaran',
            'Nama Pasien' => 'Nama  Pasien',
            'Jenis Kelamin' => 'Jenis  Kelamin',
            'Dokter' => 'Dokter',
            'Cara Bayar' => 'Cara  Bayar',
            'Penjamin' => 'Penjamin',
            'Kelas Pelayanan' => 'Kelas  Pelayanan',
            'Jenis Kasus Penyakit' => 'Jenis  Kasus  Penyakit',
            'Ruangan' => 'Ruangan',
            'Lama Rawat' => 'Lama  Rawat',
            'alasan_batal' => 'Alasan Batal',
            'status_ranap' => 'Status Ranap',
            'status_ranap_nama' => 'Status Ranap Nama',
        ];
    }
}
