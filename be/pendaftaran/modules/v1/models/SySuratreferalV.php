<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_suratreferal_v".
 *
 * @property string $tgl_pendaftaran
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property string $umur
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property string $nama_pasien
 * @property string $namadepan
 * @property string $no_rekam_medik
 * @property string $alamat_pasien
 * @property string $tanggal_lahir
 * @property string $pasienjeniskelamin
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $keluarga_namadepan
 * @property string $keluarga_nama
 * @property string $keluarga_hubungan
 * @property string $keluarga_alamat
 * @property string $diagnosa_dari
 * @property string $dokter_pengirim
 */
class SySuratreferalV extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_suratreferal_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tgl_pendaftaran', 'tanggal_lahir', 'diagnosa_dari', 'dokter_pengirim'], 'safe'],
            [['pendaftaran_id', 'pasienadmisi_id', 'jeniskasuspenyakit_id', 'carabayar_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'jeniskasuspenyakit_id', 'carabayar_id', 'kelaspelayanan_id', 'ruangan_id', 'instalasi_id'], 'integer'],
            [['alamat_pasien', 'pasienjeniskelamin', 'keluarga_hubungan', 'keluarga_alamat'], 'string'],
            [['no_pendaftaran', 'namadepan', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['umur'], 'string', 'max' => 30],
            [['jeniskasuspenyakit_nama', 'no_rekam_medik', 'keluarga_nama'], 'string', 'max' => 100],
            [['carabayar_nama', 'kelaspelayanan_nama', 'ruangan_nama', 'instalasi_nama', 'nama_pasien'], 'string', 'max' => 50],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['keluarga_namadepan'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'umur' => 'Umur',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'nama_pasien' => 'Nama Pasien',
            'namadepan' => 'Namadepan',
            'no_rekam_medik' => 'No Rekam Medik',
            'alamat_pasien' => 'Alamat Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
            'pasienjeniskelamin' => 'Pasienjeniskelamin',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'keluarga_namadepan' => 'Keluarga Namadepan',
            'keluarga_nama' => 'Keluarga Nama',
            'keluarga_hubungan' => 'Keluarga Hubungan',
            'keluarga_alamat' => 'Keluarga Alamat',
            'diagnosa_dari' => 'Diagnosa dari',
            'dokter_pengirim' => 'Dokter Pengirim',

        ];
    }
}
