<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infomonitoringbpjs_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_pendaftaran
 * @property string $tglpasienpulang
 * @property string $no_rekam_medik
 * @property string $nosep
 * @property string $nama_pasien
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property int $hak_kelas
 * @property int $pegawai_id
 * @property string $dokter_dpjp
 * @property string $diagnosa_utama
 * @property string $diagnosa_penyerta
 * @property string $diagnosa_tindakan
 * @property string $status_monitor_id
 * @property string $status_monitor
 */
class InfoMonitoringBpjsView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infomonitoringbpjs_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'penjamin_id', 'ruangan_id', 'hak_kelas', 'pegawai_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'penjamin_id', 'ruangan_id', 'hak_kelas', 'pegawai_id'], 'integer'],
            [['tgl_pendaftaran', 'tglpasienpulang'], 'safe'],
            [['diagnosa_utama', 'diagnosa_penyerta', 'diagnosa_tindakan', 'status_monitor_id', 'status_monitor'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nosep'], 'string', 'max' => 100],
            [['nama_pasien', 'carabayar_nama', 'penjamin_nama', 'ruangan_nama', 'dokter_dpjp'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'tglpasienpulang' => 'Tglpasienpulang',
            'no_rekam_medik' => 'No Rekam Medik',
            'nosep' => 'Nosep',
            'nama_pasien' => 'Nama Pasien',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'no_tempattidur' => 'No Tempattidur',
            'hak_kelas' => 'Hak Kelas',
            'pegawai_id' => 'Pegawai ID',
            'dokter_dpjp' => 'Dokter Dpjp',
            'diagnosa_utama' => 'Diagnosa Utama',
            'diagnosa_penyerta' => 'Diagnosa Penyerta',
            'diagnosa_tindakan' => 'Diagnosa Tindakan',
            'status_monitor_id' => 'Status Monitor ID',
            'status_monitor' => 'Status Monitor',
        ];
    }
}
