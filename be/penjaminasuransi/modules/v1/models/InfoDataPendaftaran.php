<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infodatapendaftaran_v".
 *
 * @property int $pendaftaran_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $pasien_id
 * @property int $penjamin_id
 * @property int $carabayar_id
 * @property int $kelaspelayanan_id
 * @property int $pasienpulang_id
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_mobile_pasien
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $kelaspelayanan_nama
 * @property string $tglpasienpulang
 */
class InfoDataPendaftaran extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infodatapendaftaran_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'instalasi_id', 'ruangan_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'kelaspelayanan_id', 'pasienpulang_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'instalasi_id', 'ruangan_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'kelaspelayanan_id', 'pasienpulang_id'], 'integer'],
            [['tgl_pendaftaran', 'tglpasienpulang'], 'safe'],
            [['no_pendaftaran', 'no_mobile_pasien'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'instalasi_nama', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'kelaspelayanan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'pasien_id' => 'Pasien ID',
            'penjamin_id' => 'Penjamin ID',
            'carabayar_id' => 'Carabayar ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pasienpulang_id' => 'Pasienpulang ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_mobile_pasien' => 'No Mobile Pasien',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'tglpasienpulang' => 'Tglpasienpulang',
        ];
    }
}
