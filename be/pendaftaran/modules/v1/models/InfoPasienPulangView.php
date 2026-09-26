<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienpulang_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $kelaspelayanan_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property int $pegawai_id
 * @property int $carakeluar_id
 * @property int $kondisikeluar_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property string $tgl_admisi
 * @property string $tgl_pendaftaran
 * @property string $tglpasienpulang
 * @property string $no_rekam_medik
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 * @property string $kelaspelayanan_nama
 * @property string $ruangan_nama
 * @property string $jeniskasuspenyakit_nama
 * @property string $nama_pegawai
 * @property string $carakeluar_nama
 * @property string $kondisikeluar_nama
 * @property int $lama_rawat
 * @property int $pasienpulang_id
 * @property int $instalasi_id
 * @property string $instalasi_nama
 */
class InfoPasienPulangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */

    public $golonganumur_id;
    public static function primaryKey()
    {
        return ['pasien_id'];
    }

    public static function tableName()
    {
        return 'infopasienpulang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'pegawai_id', 'carakeluar_id', 'kondisikeluar_id', 'carabayar_id', 'penjamin_id', 'lama_rawat', 'pasienpulang_id', 'instalasi_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'pegawai_id', 'carakeluar_id', 'kondisikeluar_id', 'carabayar_id', 'penjamin_id', 'lama_rawat', 'pasienpulang_id', 'instalasi_id'], 'integer'],
            [['tgl_admisi', 'tgl_pendaftaran', 'tglpasienpulang'], 'safe'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'kelaspelayanan_nama', 'ruangan_nama', 'nama_pegawai', 'instalasi_nama'], 'string', 'max' => 50],
            [['jeniskasuspenyakit_nama', 'carakeluar_nama', 'kondisikeluar_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'pegawai_id' => 'Pegawai ID',
            'carakeluar_id' => 'Carakeluar ID',
            'kondisikeluar_id' => 'Kondisikeluar ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'tgl_admisi' => 'Tgl Admisi',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tglpasienpulang' => 'Tglpasienpulang',
            'no_rekam_medik' => 'No Rekam Medik',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'jeniskasuspenyakit_nama' => 'Jeniskasuspenyakit Nama',
            'nama_pegawai' => 'Nama Pegawai',
            'carakeluar_nama' => 'Carakeluar Nama',
            'kondisikeluar_nama' => 'Kondisikeluar Nama',
            'lama_rawat' => 'Lama Rawat',
            'pasienpulang_id' => 'Pasienpulang ID',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
        ];
    }
}
