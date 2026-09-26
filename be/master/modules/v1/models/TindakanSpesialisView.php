<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "tindakanspesialis_v".
 *
 * @property int $spesialis_id
 * @property string $spesialis_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $daftartindakan_namalainnya
 * @property string $kategoritindakan_nama
 * @property string $kelompoktindakan_nama
 * @property string $jeniskegiatantindakan_nama
 * @property string $groupinacbg_nama
 * @property bool $is_deleted
 * @property bool $is_active
 */
class TindakanSpesialisView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakanspesialis_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spesialis_id', 'daftartindakan_id'], 'default', 'value' => null],
            [['spesialis_id', 'daftartindakan_id'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['spesialis_nama', 'kelompoktindakan_nama'], 'string', 'max' => 50],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['kategoritindakan_nama'], 'string', 'max' => 150],
            [['jeniskegiatantindakan_nama'], 'string', 'max' => 100],
            [['groupinacbg_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'spesialis_id' => 'Spesialis ID',
            'spesialis_nama' => 'Spesialis Nama',
            'daftartindakan_id' => 'Daftartindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'kategoritindakan_nama' => 'Kategoritindakan Nama',
            'kelompoktindakan_nama' => 'Kelompoktindakan Nama',
            'jeniskegiatantindakan_nama' => 'Jeniskegiatantindakan Nama',
            'groupinacbg_nama' => 'Groupinacbg Nama',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}
