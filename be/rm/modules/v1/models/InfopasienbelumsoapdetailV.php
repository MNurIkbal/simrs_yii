<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopasienbelumsoapdetail_v".
 *
 * @property int $pegawai_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $no_rm
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $nama_pasien
 * @property string $jenis_kelamin
 * @property string $alamat_pasien
 */
class InfopasienbelumsoapdetailV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopasienbelumsoapdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'instalasi_id', 'ruangan_id'], 'default', 'value' => null],
            [['pegawai_id', 'instalasi_id', 'ruangan_id'], 'integer'],
            [['tgl_pendaftaran'], 'safe'],
            [['jenis_kelamin', 'alamat_pasien'], 'string'],
            [['no_rm'], 'string', 'max' => 100],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['nama_pasien'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Pegawai ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'no_rm' => 'No Rm',
            'no_pendaftaran' => 'No Pendaftaran',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'nama_pasien' => 'Nama Pasien',
            'jenis_kelamin' => 'Jenis Kelamin',
            'alamat_pasien' => 'Alamat Pasien',
        ];
    }
}
