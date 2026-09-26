<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokter_v".
 *
 * @property integer $ruangan_id
 * @property integer $instalasi_id
 * @property string $ruangan_nama
 * @property integer $pegawai_id
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
 * @property integer $pendidikan_id
 * @property string $pendidikan_nama
 * @property integer $pendkualifikasi_id
 * @property string $pendkualifikasi_nama
 * @property string $nomorindukpegawai
 * @property integer $pangkat_id
 * @property integer $kelompokpegawai_id
 * @property integer $jabatan_id
 * @property boolean $is_deleted
 * @property boolean $instalasi_nama
 */
class DokterView extends \Doco\components\DocoActiveRecord
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
            [['ruangan_id', 'instalasi_id', 'pegawai_id', 'pendidikan_id', 'pendkualifikasi_id', 'pangkat_id', 'kelompokpegawai_id', 'jabatan_id'], 'integer'],
            [['tgl_lahirpegawai'], 'safe'],
            [['alamat_pegawai'], 'string'],
            [['is_deleted'], 'boolean'],
            [['instalasi_nama', 'ruangan_nama', 'nama_pegawai', 'nama_keluarga', 'notelp_pegawai', 'nomobile_pegawai', 'pendidikan_nama', 'pendkualifikasi_nama'], 'string', 'max' => 50],
            [['gelardepan'], 'string', 'max' => 10],
            [['gelarbelakang'], 'string', 'max' => 32],
            [['jeniskelamin', 'agama'], 'string', 'max' => 20],
            [['tempatlahir_pegawai', 'nomorindukpegawai'], 'string', 'max' => 30],
            [['golongandarah'], 'string', 'max' => 2],
            [['alamatemail'], 'string', 'max' => 100],
            [['photopegawai'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'pegawai_id' => Yii::t('app', 'Pegawai'),
            'gelardepan' => Yii::t('app', 'Gelar depan'),
            'nama_pegawai' => Yii::t('app', 'Nama pegawai'),
            'gelarbelakang' => Yii::t('app', 'Gelar belakang'),
            'jeniskelamin' => Yii::t('app', 'Jenis kelamin'),
            'nama_keluarga' => Yii::t('app', 'Nama keluarga'),
            'tempatlahir_pegawai' => Yii::t('app', 'Tempat lahir pegawai'),
            'tgl_lahirpegawai' => Yii::t('app', 'Tanggal lahir pegawai'),
            'alamat_pegawai' => Yii::t('app', 'Alamat pegawai'),
            'agama' => Yii::t('app', 'Agama'),
            'golongandarah' => Yii::t('app', 'Golongan darah'),
            'alamatemail' => Yii::t('app', 'Alamat email'),
            'notelp_pegawai' => Yii::t('app', 'No telp pegawai'),
            'nomobile_pegawai' => Yii::t('app', 'No mobile pegawai'),
            'photopegawai' => Yii::t('app', 'Photo pegawai'),
            'pendidikan_id' => Yii::t('app', 'Pendidikan'),
            'pendidikan_nama' => Yii::t('app', 'Nama pendidikan'),
            'pendkualifikasi_id' => Yii::t('app', 'Kualifikasi pendidikan'),
            'pendkualifikasi_nama' => Yii::t('app', 'Nama kualifikasi pendidikan'),
            'nomorindukpegawai' => Yii::t('app', 'Nomor induk pegawai'),
            'pangkat_id' => Yii::t('app', 'Pangkat'),
            'kelompokpegawai_id' => Yii::t('app', 'Kelompok pegawai'),
            'jabatan_id' => Yii::t('app', 'Jabatan'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
        ];
    }
}
