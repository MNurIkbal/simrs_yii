<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienrsambulan_v".
 *
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $tempat_lahir
 * @property string $tanggal_lahir
 * @property string $jenis_kelamin
 * @property string $ruangan_nama
 * @property string $instalasi_nama
 * @property string $diagnosa
 */
class PasienRsAmbulanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienrsambulan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'pasien_id'], 'integer'],
            [['tanggal_lahir'], 'safe'],
            [['diagnosa'], 'string'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['tempat_lahir'], 'string', 'max' => 25],
            [['jenis_kelamin'], 'string', 'max' => 200],
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
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'jenis_kelamin' => 'Jenis Kelamin',
            'ruangan_nama' => 'Ruangan Nama',
            'instalasi_nama' => 'Instalasi Nama',
            'diagnosa' => 'Diagnosa',
        ];
    }
}
