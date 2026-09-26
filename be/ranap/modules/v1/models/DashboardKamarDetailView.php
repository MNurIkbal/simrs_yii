<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dashboardkamardetail_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property string $tgl_admisi
 * @property int $pasienpulang_id
 * @property string $tglpasienpulang
 * @property int $pindahkamar_id
 * @property string $tgl_pindahkamar
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property string $alamat_pasien
 * @property string $jenis_kelamin
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $kamarruangan_id
 * @property string $kamarruangan_nokamar
 * @property int $kamartempattidur_id
 * @property string $no_tempattidur
 * @property int $pegawai_id
 * @property string $dr_dpjp
 * @property int $status_ranap
 * @property string $stat_ranap
 */
class DashboardKamarDetailView extends \Doco\components\DocoActiveRecord 
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dashboardkamardetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pindahkamar_id', 'pasien_id', 'carabayar_id', 'kelaspelayanan_id', 'ruangan_id', 'kamarruangan_id', 'kamartempattidur_id', 'pegawai_id', 'status_ranap'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pindahkamar_id', 'pasien_id', 'carabayar_id', 'kelaspelayanan_id', 'ruangan_id', 'kamarruangan_id', 'kamartempattidur_id', 'pegawai_id', 'status_ranap'], 'integer'],
            [['tgl_admisi', 'tglpasienpulang', 'tgl_pindahkamar'], 'safe'],
            [['alamat_pasien'], 'string'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien', 'carabayar_nama', 'kelaspelayanan_nama', 'ruangan_nama', 'dr_dpjp'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['jenis_kelamin', 'stat_ranap'], 'string', 'max' => 200],
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
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_admisi' => 'Tgl Admisi',
            'pasienpulang_id' => 'Pasienpulang ID',
            'tglpasienpulang' => 'Tglpasienpulang',
            'pindahkamar_id' => 'Pindahkamar ID',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'alamat_pasien' => 'Alamat Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'kamarruangan_id' => 'Kamarruangan ID',
            'kamarruangan_nokamar' => 'Kamarruangan Nokamar',
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'no_tempattidur' => 'No Tempattidur',
            'pegawai_id' => 'Pegawai ID',
            'dr_dpjp' => 'Dr Dpjp',
            'status_ranap' => 'Status Ranap',
            'stat_ranap' => 'Stat Ranap',
        ];
    }
}
