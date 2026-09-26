<?php

namespace app\modules\bankdarah\models;

use Yii;

/**
 * This is the model class for table "infopasienmasihdirawat_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $nama_panggilan
 * @property string $nama_ibu
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $umur
 * @property int $jenis_kelaminid
 * @property string $jenis_kelamin
 * @property int $golongandarah_id
 * @property string $golongandarah_nama
 * @property string $alamat_pasien
 * @property int $rt
 * @property int $rw
 * @property string $ruangan_nama
 * @property string $instalasi_nama
 * @property string $diagnosa
 * @property int $kelaspelayanan_id
 * @property int $penjamin_id
 * @property string $kadar_hb
 * @property string $tgl_pendaftaran
 */
class InfoPasienMasihDirawatView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienmasihdirawat_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'jenis_kelaminid', 'golongandarah_id', 'rt', 'rw', 'kelaspelayanan_id', 'penjamin_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'jenis_kelaminid', 'golongandarah_id', 'rt', 'rw', 'kelaspelayanan_id', 'penjamin_id'], 'integer'],
            [['tanggal_lahir', 'tgl_pendaftaran'], 'safe'],
            [['jenis_kelamin', 'golongandarah_nama', 'alamat_pasien', 'diagnosa', 'kadar_hb'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'nama_ibu', 'ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['nama_panggilan', 'umur'], 'string', 'max' => 30],
            [['tempat_lahir'], 'string', 'max' => 25],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'nama_panggilan' => 'Nama Panggilan',
            'nama_ibu' => 'Nama Ibu',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'jenis_kelaminid' => 'Jenis Kelaminid',
            'jenis_kelamin' => 'Jenis Kelamin',
            'golongandarah_id' => 'Golongandarah ID',
            'golongandarah_nama' => 'Golongandarah Nama',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_nama' => 'Instalasi Nama',
            'diagnosa' => 'Diagnosa',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'kadar_hb' => 'Kadar Hb',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
        ];
    }
}
