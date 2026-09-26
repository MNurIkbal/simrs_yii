<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "daftartindakan_m".
 *
 * @property int $daftartindakan_id
 * @property int $kategoritindakan_id
 * @property int $kelompoktindakan_id
 * @property int $komponenunit_id
 * @property int $jeniskegiatantindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $tindakanmedis_nama
 * @property string $daftartindakan_namalainnya
 * @property string $daftartindakan_katakunci
 * @property bool $daftartindakan_karcis
 * @property bool $daftartindakan_visite
 * @property bool $daftartindakan_konsul
 * @property bool $daftartindakan_akomodasi
 * @property bool $daftartindakan_tindakan
 * @property bool $daftartindakan_observasi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property bool $is_akomodasi
 */
class DaftarTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daftartindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_kode','daftartindakan_namalainnya','daftartindakan_nama',
              'kelompoktindakan_id'], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['catatan', 'created_date', 'last_modified_date', 'deleted_date', 'isGroupInaCbg', 'is_akomodasi', 'is_konsultasi'], 'safe'],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'tindakanmedis_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['daftartindakan_katakunci'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftartindakan ID',
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'groupinacbg_id' => 'Group INA CBGS',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'catatan' => 'Catatan',
            'is_konsultasi' => 'Konsultasi'
        ];
    }
}