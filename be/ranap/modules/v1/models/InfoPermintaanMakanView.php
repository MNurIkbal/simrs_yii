<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopermintaanmakan_v".
 *
 * @property int $permintaaanmakan_id
 * @property int $pendaftaran_id
 * @property string $no_pendaftaran
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property string $no_permintaanmakan
 * @property string $nama_pegawai
 */
class InfoPermintaanMakanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopermintaanmakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaaanmakan_id', 'pendaftaran_id'], 'default', 'value' => null],
            [['permintaaanmakan_id', 'pendaftaran_id'], 'integer'],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien', 'nama_pegawai'], 'string', 'max' => 50],
            [['no_permintaanmakan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaaanmakan_id' => 'Permintaaanmakan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekam_medik' => 'No Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'no_permintaanmakan' => 'No Permintaanmakan',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
