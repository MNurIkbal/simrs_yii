<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infosisaantrian_v".
 *
 * @property int $antrian_id
 * @property string $no_antrian
 * @property int $groupcarabayar_id
 * @property string $group_carabayar
 * @property int $klasifikasipasien_id
 * @property string $klasifikasipasien_nama
 * @property int $status_antrian
 * @property int $jenisantrian_id
 * @property bool $is_online
 * @property string $status_antrian_nama
 * @property int $konfigantrian_id
 * @property int $loket_id
 */
class InfoSisaAntrianView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infosisaantrian_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['antrian_id', 'groupcarabayar_id', 'klasifikasipasien_id', 'status_antrian', 'jenisantrian_id', 'konfigantrian_id', 'loket_id'], 'default', 'value' => null],
            [['antrian_id', 'groupcarabayar_id', 'klasifikasipasien_id', 'status_antrian', 'jenisantrian_id', 'konfigantrian_id', 'loket_id'], 'integer'],
            [['is_online'], 'boolean'],
            [['status_antrian_nama'], 'string'],
            [['no_antrian'], 'string', 'max' => 12],
            [['group_carabayar'], 'string', 'max' => 200],
            [['klasifikasipasien_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => 'Antrian ID',
            'no_antrian' => 'No Antrian',
            'groupcarabayar_id' => 'Groupcarabayar ID',
            'group_carabayar' => 'Group Carabayar',
            'klasifikasipasien_id' => 'Klasifikasipasien ID',
            'klasifikasipasien_nama' => 'Klasifikasipasien Nama',
            'status_antrian' => 'Status Antrian',
            'jenisantrian_id' => 'Jenisantrian ID',
            'is_online' => 'Is Online',
            'status_antrian_nama' => 'Status Antrian Nama',
            'konfigantrian_id' => 'Konfigantrian ID',
            'loket_id' => 'Loket ID',
        ];
    }
}
