<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienmeninggal_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $no_pendaftaran
 * @property int $pasienpulang_id
 * @property string $tgl_meninggal
 * @property int $pasien_id
 * @property string $nama_pasien
 * @property string $no_rekam_medik
 * @property string $jeniskelamin
 * @property string $jenis_kelamin
 * @property string $ruanganakhir_id
 * @property string $ruangan_nama
 * @property int $penanggungjawab_id
 * @property string $penanggungjawab_nama
 * @property int $pasienmasukpenunjang_id
 * @property string $status_periksa
 * @property string $status_periksa_nama
 */
class InfoPasienMeninggal extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public $diagnosa_utama;
    
    public static function tableName()
    {
        return 'infopasienmeninggal_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pasien_id', 'ruanganakhir_id', 'penanggungjawab_id', 'pasienmasukpenunjang_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasienpulang_id', 'pasien_id', 'ruanganakhir_id', 'penanggungjawab_id', 'pasienmasukpenunjang_id'], 'integer'],
            [['tgl_meninggal'], 'safe'],
            [['no_pendaftaran', 'jeniskelamin'], 'string', 'max' => 20],
            [['nama_pasien', 'ruangan_nama', 'penanggungjawab_nama', 'status_periksa'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['jenis_kelamin', 'status_periksa_nama'], 'string', 'max' => 200],
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
            'pasienpulang_id' => 'Pasienpulang ID',
            'tgl_meninggal' => 'Tgl Meninggal',
            'pasien_id' => 'Pasien ID',
            'nama_pasien' => 'Nama Pasien',
            'no_rekam_medik' => 'No Rekam Medik',
            'jeniskelamin' => 'Jeniskelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'ruanganakhir_id' => 'Ruanganakhir ID',
            'ruangan_nama' => 'Ruangan Nama',
            'penanggungjawab_id' => 'Penanggungjawab ID',
            'penanggungjawab_nama' => 'Penanggungjawab Nama',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'status_periksa' => 'Status Periksa',
            'status_periksa_nama' => 'Status Periksa Nama',
        ];
    }
}
