<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "infojanjipoli_v".
 *
 * @property int $buatjanjipoli_id
 * @property string $tgl_buatjanji
 * @property string $antrian_id
 * @property string $no_antrian
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $alamat_pasien
 * @property string $no_telepon_pasien
 * @property string $no_mobile_pasien
 * @property string $alamatemail
 * @property string $hari
 * @property string $tgl_jadwal
 * @property bool $is_rencanakontrol
 * @property string $status_janji
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $keterangan_buatjanji
 * @property bool $by_phone
 */
class InfReservasiPoliklinikForm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infojanjipoli_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['buatjanjipoli_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'instalasi_id', 'carabayar_id', 'penjamin_id'], 'default', 'value' => null],
            [['buatjanjipoli_id', 'pegawai_id', 'ruangan_id', 'pasien_id', 'instalasi_id', 'carabayar_id', 'penjamin_id'], 'integer'],
            [['tgl_buatjanji', 'tgl_jadwal', 'tgl_pendaftaran'], 'safe'],
            [['alamat_pasien', 'keterangan_buatjanji'], 'string'],
            [['is_rencanakontrol', 'by_phone'], 'boolean'],
            [['antrian_id'], 'string', 'max' => 32],
            [['no_antrian'], 'string', 'max' => 6],
            [['nama_pegawai', 'ruangan_nama', 'nama_pasien', 'instalasi_nama', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['no_telepon_pasien'], 'string', 'max' => 15],
            [['no_mobile_pasien', 'no_pendaftaran'], 'string', 'max' => 20],
            [['alamatemail'], 'string', 'max' => 100],
            [['hari', 'status_janji'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'buatjanjipoli_id' => 'Buatjanjipoli ID',
            'tgl_buatjanji' => 'Tgl Buatjanji',
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'alamat_pasien' => 'Alamat Pasien',
            'no_telepon_pasien' => 'No Telepon Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'alamatemail' => 'Alamatemail',
            'hari' => 'Hari',
            'tgl_jadwal' => 'Tgl Jadwal',
            'is_rencanakontrol' => 'Is Rencanakontrol',
            'status_janji' => 'Status Janji',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'keterangan_buatjanji' => 'Keterangan Buatjanji',
            'by_phone' => 'By Phone',
        ];
    }
}
