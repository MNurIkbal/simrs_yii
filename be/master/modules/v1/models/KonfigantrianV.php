<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigantrian_v".
 *
 * @property int $konfigantrian_id
 * @property int $layarantrian_id
 * @property int $jenisantrian_id
 * @property int $fungsiantrian_id
 * @property int $carabayar_id
 * @property string $layarantrian_nama
 * @property string $jenis_antrian
 * @property string $fungsi_antrian
 * @property string $lookup_value
 * @property string $carabayar_nama
 * @property bool $is_default
 * @property bool $is_penjamin
 */
class KonfigantrianV extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigantrian_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id'], 'default', 'value' => null],
            [['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id'], 'integer'],
            [['is_default', 'is_penjamin'], 'boolean'],
            [['layarantrian_nama'], 'string', 'max' => 100],
            [['jenis_antrian', 'fungsi_antrian', 'lookup_value'], 'string', 'max' => 200],
            [['carabayar_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigantrian_id' => 'Konfigantrian ID',
            'layarantrian_id' => 'Layarantrian ID',
            'jenisantrian_id' => 'Jenisantrian ID',
            'fungsiantrian_id' => 'Fungsiantrian ID',
            'carabayar_id' => 'Carabayar ID',
            'layarantrian_nama' => 'Layarantrian Nama',
            'jenis_antrian' => 'Jenis Antrian',
            'fungsi_antrian' => 'Fungsi Antrian',
            'lookup_value' => 'Lookup Value',
            'carabayar_nama' => 'Carabayar Nama',
            'is_default' => 'Is Default',
            'is_penjamin' => 'Is Penjamin',
        ];
    }
}
