<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infodokterbelumisirm_v".
 *
 * @property string $jenis
 * @property string $tgl_pendaftaran
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $ruangan
 * @property int $pegawai_id
 * @property string $dokter
 * @property string $jumlah
 * @property string $instalasi
 */
class InfodokterbelumisirmV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infodokterbelumisirm_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis', 'instalasi'], 'string'],
            [['tgl_pendaftaran'], 'safe'],
            [['instalasi_id', 'ruangan_id', 'pegawai_id'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id', 'pegawai_id'], 'integer'],
            [['ruangan', 'dokter'], 'string', 'max' => 50],
            [['jumlah'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis' => 'Jenis',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'ruangan' => 'Ruangan',
            'pegawai_id' => 'Pegawai ID',
            'dokter' => 'Dokter',
            'jumlah' => 'Jumlah',
            'instalasi' => 'Instalasi Nama',
        ];
    }
}
