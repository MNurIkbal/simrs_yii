<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reseptemp_v".
 *
 * @property int $reseptemp_id
 * @property int $reseptemp_nama
 * @property int $dokter_id
 * @property string $nama_pegawai
 */
class ResepTempView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reseptemp_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reseptemp_id', 'reseptemp_nama', 'dokter_id'], 'default', 'value' => null],
            [['reseptemp_id', 'reseptemp_nama', 'dokter_id'], 'integer'],
            [['nama_pegawai'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptemp_id' => 'Reseptemp ID',
            'reseptemp_nama' => 'Reseptemp Nama',
            'dokter_id' => 'Dokter ID',
            'nama_pegawai' => 'Nama Pegawai',
        ];
    }
}
