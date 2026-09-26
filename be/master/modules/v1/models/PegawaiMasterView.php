<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pegawai_master_v".
 *
 * @property int $pegawai_id
 * @property string $gelardepan
 * @property string $nama_pegawai
 * @property string $gelarbelakang
 * @property string $jeniskelamin
 * @property string $tempatlahir_pegawai
 * @property string $tgl_lahirpegawai
 * @property string $alamat_pegawai
 * @property string $agama
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
 * @property string $jabatan_nama
 * @property string $pangkat_nama
 * @property string $kelompokpegawai_nama
 * @property string $kelompokpegawai_namalainnya
 * @property string $kelompokpegawai_fungsi
 */
class PegawaiMasterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pegawai_master_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'pendidikan_id', 'pendkualifikasi_id', 'pangkat_id', 'kelompokpegawai_id', 'jabatan_id'], 'default', 'value' => null],
            [['pegawai_id', 'pendidikan_id', 'pendkualifikasi_id', 'pangkat_id', 'kelompokpegawai_id', 'jabatan_id'], 'integer'],
            [['tgl_lahirpegawai','kode_dokter_bpjs','nama_dokter_bpjs'], 'safe'],
            [['alamat_pegawai', 'kelompokpegawai_fungsi'], 'string'],
            [['gelardepan'], 'string', 'max' => 10],
            [['nama_pegawai', 'notelp_pegawai', 'nomobile_pegawai', 'pendidikan_nama', 'pendkualifikasi_nama', 'pangkat_nama'], 'string', 'max' => 50],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama'], 'string', 'max' => 20],
            [['tempatlahir_pegawai', 'nomorindukpegawai', 'kelompokpegawai_nama', 'kelompokpegawai_namalainnya'], 'string', 'max' => 30],
            [['alamatemail', 'jabatan_nama'], 'string', 'max' => 100],
            [['photopegawai'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai ID',
            'gelardepan' => 'Gelardepan',
            'nama_pegawai' => 'Nama Pegawai',
            'gelarbelakang' => 'Gelarbelakang',
            'jeniskelamin' => 'Jeniskelamin',
            'tempatlahir_pegawai' => 'Tempatlahir Pegawai',
            'tgl_lahirpegawai' => 'Tgl Lahirpegawai',
            'alamat_pegawai' => 'Alamat Pegawai',
            'agama' => 'Agama',
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
            'jabatan_nama' => 'Jabatan Nama',
            'pangkat_nama' => 'Pangkat Nama',
            'kelompokpegawai_nama' => 'Kelompokpegawai Nama',
            'kelompokpegawai_namalainnya' => 'Kelompokpegawai Namalainnya',
            'kelompokpegawai_fungsi' => 'Kelompokpegawai Fungsi',
        ];
    }
}
