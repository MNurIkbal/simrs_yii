<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_v".
 *
 * @property integer $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 * @property string $jenisidentitas
 * @property string $identitas
 * @property string $no_identitas_pasien
 * @property string $namadepan
 * @property string $nama_depan
 * @property string $nama_bin
 * @property string $tempat_lahir
 * @property string $jeniskelamin
 * @property string $jenis_kelamin
 * @property string $statusperkawinan
 * @property string $status_perkawinan
 * @property string $nama_ibu
 * @property string $alamat_sekarang
 * @property string $alamat_pasien
 * @property integer $rt
 * @property integer $rw
 * @property integer $propinsi_id
 * @property string $propinsi_nama
 */

class PasienV extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasien_v';
    }

    public static function primaryKey()
    {
        return ["pasien_id"];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id', 'rt', 'rw', 'propinsi_id'], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir', 'photopasien'], 'safe'],
            [['alamat_sekarang', 'alamat_pasien'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'nama_ibu', 'propinsi_nama'], 'string', 'max' => 50],
            [['jenisidentitas', 'namadepan', 'jeniskelamin', 'statusperkawinan'], 'string', 'max' => 20],
            [['identitas', 'nama_depan', 'jenis_kelamin', 'status_perkawinan'], 'string', 'max' => 200],
            [['no_identitas_pasien', 'nama_bin'], 'string', 'max' => 30],
            [['tempat_lahir'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenisidentitas' => 'Jenisidentitas',
            'identitas' => 'Identitas',
            'no_identitas_pasien' => 'No Identitas Pasien',
            'namadepan' => 'Namadepan',
            'nama_depan' => 'Nama Depan',
            'nama_bin' => 'Nama Bin',
            'tempat_lahir' => 'Tempat Lahir',
            'jeniskelamin' => 'Jeniskelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'statusperkawinan' => 'Statusperkawinan',
            'status_perkawinan' => 'Status Perkawinan',
            'nama_ibu' => 'Nama Ibu',
            'alamat_sekarang' => 'Alamat Sekarang',
            'alamat_pasien' => 'Alamat Pasien',
            'rt' => 'Rt',
            'rw' => 'Rw',
            'propinsi_id' => 'Propinsi ID',
            'propinsi_nama' => 'Propinsi Nama',
            'alasan_ubahdata' => 'Alasan Ubah Data',
        ];
    }
}
