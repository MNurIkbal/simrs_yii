<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokter_v".
 *
 * @property int $ruangan_id
 * @property int $instalasi_id
 * @property string $ruangan_nama
 * @property int $pegawai_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $nama_keluarga
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $alamat_pegawai
 * @property string $agama
 * @property string $golongandarah
 * @property string $alamatemail
 * @property string $notelp_pegawai
 * @property string $nomobile_pegawai
 * @property string $photopegawai
 * @property int $pendidikan_id
 * @property string $pendidikan_nama
 * @property int $pendkualifikasi_id
 * @property string $pendkualifikasi_nama
 * @property string $nomorindukpegawai
 * @property int $pangkat_id
 * @property int $kelompokpegawai_id
 * @property int $jabatan_id
 * @property bool $is_deleted
 * @property string $instalasi_nama
 * @property string $jabatan_nama
 */
class DokterV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokter_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'instalasi_id', 'pegawai_id', 'pendidikan_id', 'pendkualifikasi_id', 'pangkat_id', 'kelompokpegawai_id', 'jabatan_id'], 'default', 'value' => null],
            [['ruangan_id', 'instalasi_id', 'pegawai_id', 'pendidikan_id', 'pendkualifikasi_id', 'pangkat_id', 'kelompokpegawai_id', 'jabatan_id'], 'integer'],
            [['tgl_lahirpegawai'], 'safe'],
            [['alamat_pegawai'], 'string'],
            [['is_deleted'], 'boolean'],
            [['ruangan_nama', 'nama_pegawai', 'nama_keluarga', 'notelp_pegawai', 'nomobile_pegawai', 'pendidikan_nama', 'pendkualifikasi_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['gelardepan'], 'string', 'max' => 10],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama'], 'string', 'max' => 20],
            [['tempatlahir_pegawai', 'nomorindukpegawai'], 'string', 'max' => 30],
            [['golongandarah'], 'string', 'max' => 2],
            [['alamatemail', 'jabatan_nama'], 'string', 'max' => 100],
            [['photopegawai'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => 'Ruangan ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pegawai_id' => 'Pegawai ID',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'jeniskelamin' => 'Jeniskelamin',
            'nama_keluarga' => 'Nama Keluarga',
            'tempatlahir_pegawai' => 'Tempatlahir Pegawai',
            'tgl_lahirpegawai' => 'Tgl Lahirpegawai',
            'alamat_pegawai' => 'Alamat Pegawai',
            'agama' => 'Agama',
            'golongandarah' => 'Golongandarah',
            'alamatemail' => 'Alamatemail',
            'notelp_pegawai' => 'Notelp Pegawai',
            'nomobile_pegawai' => 'Nomobile Pegawai',
            'photopegawai' => 'Photopegawai',
            'pendidikan_id' => 'Pendidikan ID',
            'pendidikan_nama' => 'Pendidikan Nama',
            'pendkualifikasi_id' => 'Pendkualifikasi ID',
            'pendkualifikasi_nama' => 'Pendkualifikasi Nama',
            'nomorindukpegawai' => 'Nomorindukpegawai',
            'pangkat_id' => 'Pangkat ID',
            'kelompokpegawai_id' => 'Kelompokpegawai ID',
            'jabatan_id' => 'Jabatan ID',
            'is_deleted' => 'Is Deleted',
            'instalasi_nama' => 'Instalasi Nama',
            'jabatan_nama' => 'Jabatan Nama',
        ];
    }
}
