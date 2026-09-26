<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-20 16:04:47
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforeseptur_v".
 *
 * @property int $reseptur_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $ruangan_id
 * @property int $ruanganreseptur_id
 * @property string $tglreseptur
 * @property string $noresep
 * @property int $penjualanresep_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property string $penjamin_nama
 * @property string $ruangan_tujuan
 * @property string $ruangan_reseptur
 * @property string $status_reseptur
 * @property int $pegawai_id
 * @property string $nama_pegawai
 * @property int $instalasi_reseptur_id
 * @property string $instalasi_reseptur
 * @property int $instalasi_tujuan_id
 * @property string $instalasi_tujuan
 * @property double $total_harganetto
 * @property string $no_antrian
 * @property int $status_reseptur_id
 */
class InfoResepturView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforeseptur_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reseptur_id', 'pasien_id', 'pendaftaran_id', 'pasienadmisi_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'ruanganreseptur_id', 'penjualanresep_id', 'pegawai_id', 'instalasi_reseptur_id', 'instalasi_tujuan_id', 'status_reseptur_id'], 'default', 'value' => null],
            [['reseptur_id', 'pasien_id', 'pendaftaran_id', 'pasienadmisi_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'ruanganreseptur_id', 'penjualanresep_id', 'pegawai_id', 'instalasi_reseptur_id', 'instalasi_tujuan_id', 'status_reseptur_id'], 'integer'],
            [['tglreseptur'], 'safe'],
            [['total_harganetto'], 'number'],
            [['noresep', 'nama_pasien', 'carabayar_nama', 'penjamin_nama', 'ruangan_tujuan', 'ruangan_reseptur', 'nama_pegawai', 'instalasi_reseptur', 'instalasi_tujuan'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['status_reseptur'], 'string', 'max' => 200],
            [['no_antrian'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptur_id' => 'Reseptur ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'ruangan_id' => 'Ruangan ID',
            'ruanganreseptur_id' => 'Ruanganreseptur ID',
            'tglreseptur' => 'Tglreseptur',
            'noresep' => 'Noresep',
            'penjualanresep_id' => 'Penjualanresep ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_nama' => 'Penjamin Nama',
            'ruangan_tujuan' => 'Ruangan Tujuan',
            'ruangan_reseptur' => 'Ruangan Reseptur',
            'status_reseptur' => 'Status Reseptur',
            'pegawai_id' => 'Pegawai ID',
            'nama_pegawai' => 'Nama Pegawai',
            'instalasi_reseptur_id' => 'Instalasi Reseptur ID',
            'instalasi_reseptur' => 'Instalasi Reseptur',
            'instalasi_tujuan_id' => 'Instalasi Tujuan ID',
            'instalasi_tujuan' => 'Instalasi Tujuan',
            'total_harganetto' => 'Total Harganetto',
            'no_antrian' => 'No Antrian',
            'status_reseptur_id' => 'Status Reseptur ID',
        ];
    }
}