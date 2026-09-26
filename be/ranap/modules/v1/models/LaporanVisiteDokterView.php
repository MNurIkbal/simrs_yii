<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-09 14:23
 */
namespace app\modules\v1\models;

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
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran ID'),
            'No. Pendaftaran' => Yii::t('app', 'No Pendaftaran'),
            'Tanggal Admisi' => Yii::t('app', 'Tanggal Admisi'),
            'Tanggal Visite' => Yii::t('app', 'Tanggal Visite'),
            'No. Rekam Medik' => Yii::t('app', 'No Rekam Medik'),
            'Nama Pasien' => Yii::t('app', 'Nama Pasien'),
            'Cara Bayar' => Yii::t('app', 'Cara Bayar'),
            'Penjamin' => Yii::t('app', 'Penjamin'),
            'Jenis Kelamin' => Yii::t('app', 'Jenis Kelamin'),
            'Kasus Penyakit' => Yii::t('app', 'Kasus Penyakit'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'Ruangan' => Yii::t('app', 'Ruangan'),
            'Kamar' => Yii::t('app', 'Kamar'),
            'Bed' => Yii::t('app', 'Bed'),
            'Dokter Penanggung Jawab' => Yii::t('app', 'Dokter Penanggung Jawab'),
            'kelompoktindakan_nama' => Yii::t('app', 'Kelompoktindakan Nama'),
            'Jenis Visite' => Yii::t('app', 'Jenis Visite'),
            'dokvisite_id' => Yii::t('app', 'Dokvisite ID'),
            'Dokter Visite' => Yii::t('app', 'Dokter Visite'),
        ];
    }
}
