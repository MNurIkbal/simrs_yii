<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "laporanvisitedokter_v".
 *
 * @property int $pendaftaran_id
 * @property string $No. Pendaftaran
 * @property string $Tanggal Admisi
 * @property string $Tanggal Visite
 * @property string $No. Rekam Medik
 * @property string $Nama Pasien
 * @property string $Cara Bayar
 * @property string $Penjamin
 * @property string $Jenis Kelamin
 * @property string $Kasus Penyakit
 * @property int $ruangan_id
 * @property string $Ruangan
 * @property string $Kamar
 * @property string $Bed
 * @property string $Dokter Penanggung Jawab
 * @property string $kelompoktindakan_nama
 * @property string $Jenis Visite
 * @property int $dokvisite_id
 * @property string $Dokter Visite
 */
class LaporanVisiteDokterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanvisitedokter_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'ruangan_id', 'dokvisite_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'ruangan_id', 'dokvisite_id'], 'integer'],
            [['Tanggal Admisi', 'Tanggal Visite'], 'safe'],
            [['No. Pendaftaran'], 'string', 'max' => 20],
            [['No. Rekam Medik'], 'string', 'max' => 10],
            [['Nama Pasien', 'Cara Bayar', 'Penjamin', 'Ruangan', 'Dokter Penanggung Jawab', 'kelompoktindakan_nama', 'Dokter Visite'], 'string', 'max' => 50],
            [['Jenis Kelamin', 'Jenis Visite'], 'string', 'max' => 200],
            [['Kasus Penyakit'], 'string', 'max' => 100],
            [['Kamar'], 'string', 'max' => 25],
            [['Bed'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'No. Pendaftaran' => 'No   Pendaftaran',
            'Tanggal Admisi' => 'Tanggal  Admisi',
            'Tanggal Visite' => 'Tanggal  Visite',
            'No. Rekam Medik' => 'No   Rekam  Medik',
            'Nama Pasien' => 'Nama  Pasien',
            'Cara Bayar' => 'Cara  Bayar',
            'Penjamin' => 'Penjamin',
            'Jenis Kelamin' => 'Jenis  Kelamin',
            'Kasus Penyakit' => 'Kasus  Penyakit',
            'ruangan_id' => 'Ruangan ID',
            'Ruangan' => 'Ruangan',
            'Kamar' => 'Kamar',
            'Bed' => 'Bed',
            'Dokter Penanggung Jawab' => 'Dokter  Penanggung  Jawab',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'Jenis Visite' => 'Jenis  Visite',
            'dokvisite_id' => 'Dokvisite ID',
            'Dokter Visite' => 'Dokter  Visite',
        ];
    }
}
