<?php

namespace SirsCore\models;

use Yii;

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
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'daftartindakan_nama'], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'daftartindakan_akomodasi', 'daftartindakan_tindakan', 'daftartindakan_observasi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
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
            'komponenunit_id' => 'Komponenunit ID',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'daftartindakan_karcis' => 'Daftartindakan Karcis',
            'daftartindakan_visite' => 'Daftartindakan Visite',
            'daftartindakan_konsul' => 'Daftartindakan Konsul',
            'daftartindakan_akomodasi' => 'Daftartindakan Akomodasi',
            'daftartindakan_tindakan' => 'Daftartindakan Tindakan',
            'daftartindakan_observasi' => 'Daftartindakan Observasi',
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
        ];
    }

}