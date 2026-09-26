<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-30 14:59:16
 */

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
 * @property string $jenis_antrian
 * @property string $fungsi_antrian
 * @property string $lookup_value
 * @property string $carabayar_nama
 * @property bool $is_default
 * @property bool $is_penjamin
 * @property string $kode_antrian
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property int $groupcarabayar_id
 * @property string $group_carabayar
 * @property int $penomoran_id
 * @property int $klasifikasipasien_id
 * @property string $klasifikasipasien_nama
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $group_id
 */
class KonfigAntrianView extends \yii\db\ActiveRecord
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
            [['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id', 'instalasi_id', 'groupcarabayar_id', 'penomoran_id', 'klasifikasipasien_id', 'ruangan_id', 'group_id'], 'default', 'value' => null],
            [['konfigantrian_id', 'layarantrian_id', 'jenisantrian_id', 'fungsiantrian_id', 'carabayar_id', 'instalasi_id', 'groupcarabayar_id', 'penomoran_id', 'klasifikasipasien_id', 'ruangan_id', 'group_id'], 'integer'],
            [['is_default', 'is_penjamin'], 'boolean'],
            [['jenis_antrian', 'fungsi_antrian', 'lookup_value', 'group_carabayar'], 'string', 'max' => 200],
            [['carabayar_nama', 'instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
            [['kode_antrian', 'klasifikasipasien_nama'], 'string', 'max' => 255],
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
            'jenis_antrian' => 'Jenis Antrian',
            'fungsi_antrian' => 'Fungsi Antrian',
            'lookup_value' => 'Lookup Value',
            'carabayar_nama' => 'Carabayar Nama',
            'is_default' => 'Is Default',
            'is_penjamin' => 'Is Penjamin',
            'kode_antrian' => 'Kode Antrian',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_nama' => 'Instalasi Nama',
            'groupcarabayar_id' => 'Groupcarabayar ID',
            'group_carabayar' => 'Group Carabayar',
            'penomoran_id' => 'Penomoran ID',
            'klasifikasipasien_id' => 'Klasifikasipasien ID',
            'klasifikasipasien_nama' => 'Klasifikasipasien Nama',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Ruangan Nama',
            'group_id' => 'Group ID',
        ];
    }
}
