<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\models;

use Yii;

class MappingPoli extends \Doco\components\DocoActiveRecord {
    public static function primaryKey() {
        return 'mapping_poli_id';
    }
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'mapping_poli_m';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kodepoli_map', 'kodepoli_id', 'nama', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kodepoli_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama', 'ruangan_id'], 'required'],
            [['kodepoli_map', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kodepoli_map' => 'Kode Poli BPJS',
            'kodepoli_id' => 'ID Poli',
            'nama' => 'Nama Poli',
            'ruangan_id' => 'Ruangan ID',
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