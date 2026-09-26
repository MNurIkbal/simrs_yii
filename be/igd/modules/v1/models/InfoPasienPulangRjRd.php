<?php

/**
 * @Author: iqbal@docotel.cm
 * @Date:   2018-08-6 11:36:12
 * @Last Modified by:
 * @Last Modified time:
 * @Description:
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infokunjunganrj_v".
 *
 * @property int $pendaftaran_id
 * @property int $carabayar_id
 * @property int $ruanganakhir_id
 * @property int $penjamin_id
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $kondisikeluar_id
 * @property int $pasienpulang_id
 * @property date $tgl_pendaftaran
 * @property date $tglpasienpulang
 * @property string $no_rekam_medik
 * @property string $no_pendaftaran
 * @property string $nama_pasien
 * @property string $jenis_kelamin
 * @property string $kelaspelayanan_nama
 * @property string $ruangan_nama
 * @property string $jeniskasuspenyakit_nama
 * @property string $penjamin_nama
 * @property string $dokter
 * @property string $kondisikeluar_nama
 * @property string $no_telepon_pasien
 */
class InfoPasienPulangRjRd extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienpulangrjrd_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'carabayar_id', 'tgl_pendaftaran', 'tglpasienpulang', 'no_rekam_medik', 'no_pendaftaran', 'nama_pasien', 'jenis_kelamin', 'kelaspelayanan_nama', 'ruanganakhir_id', 'ruangan_nama', 'jeniskasuspenyakit_nama', 'penjamin_id', 'penjamin_nama', 'pegawai_id', 'dokter', 'carakeluar_nama', 'instalasi_id', 'kondisikeluar_id', 'kondisikeluar_nama', 'pasienpulang_id', 'no_telepon_pasien'], 'default', 'value' => null],
            [['pendaftaran_id', 'carabayar_id', 'ruanganakhir_id', 'penjamin_id', 'pegawai_id', 'instalasi_id', 'kondisikeluar_id', 'pasienpulang_id'], 'integer'],

            [['no_rekam_medik', 'no_pendaftaran', 'nama_pasien', 'jenis_kelamin', 'kelaspelayanan_nama', 'ruangan_nama', 'jeniskasuspenyakit_nama', 'penjamin_nama', 'dokter', 'carakeluar_nama', 'kondisikeluar_nama', 'no_telepon_pasien'], 'string'],
            [['no_rekam_medik', 'no_pendaftaran', 'jenis_kelamin'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'carabayar_id' => 'Carabayar ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tglpasienpulang' => 'Tgl Pasien Pulang',
            'no_rekam_medik' => 'No Rekam Medik',
            'no_pendaftaran' => 'No Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'kelaspelayanan_nama' => 'Nama Kelas Pelayanan',
            'ruanganakhir_id' => 'Ruangan Akhir ID',
            'ruangan_nama' => 'Nama Ruangan',
            'jeniskasuspenyakit_nama' => 'Nama Jenis Kasus Penyakit',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'pegawai_id' => 'Pegawai ID',
            'dokter' => 'Dokter',
            'carakeluar_nama' => 'Nama Cara Keluar',
            'instalasi_id' => 'Instalasi ID',
            'kondisikeluar_id' => 'Kondisi Keluar ID',
            'kondisikeluar_nama' => 'Nama Kondisi Keluar',
            'pasienpulang_id' => 'Pasien Pulang ID',
            'no_telepon_pasien' => 'No Telepon',
        ];
    }
}
