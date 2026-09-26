<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inforuanganinstalasipeg_v".
 *
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $nama_pegawai
 */
class InforuanganinstalasipegV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inforuanganinstalasipeg_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_id', 'pegawai_id'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id', 'pegawai_id'], 'integer'],
            [['instalasi_nama', 'ruangan_nama', 'nama_pegawai'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'instalasi_nama' => 'Instalasi Nama',
            'ruangan_nama' => 'Ruangan Nama',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
