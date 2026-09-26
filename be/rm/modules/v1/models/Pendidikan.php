<?php

namespace app\modules\v1\models;

use Yii;

class Pendidikan extends \Doco\components\DocoActiveRecord
{

    public static function tableName()
    {
        return 'pendidikan_m';
    }

    public $indexing_id;
    public $pendidikan_urutan;
    public $pendidikan_nama;
    public $pendidikan_namalainnya;
    public $additional_data;
    public $created_by;
    public $created_date;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['indexing_id', 'pendidikan_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['indexing_id', 'pendidikan_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendidikan_urutan', 'pendidikan_nama','indexing_id'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date','indexing_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pendidikan_nama', 'pendidikan_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pendidikan_id' => 'Pendidikan ID',
            'indexing_id' => 'Indexing',
            'pendidikan_urutan' => 'Pendidikan Urutan',
            'pendidikan_nama' => 'Pendidikan Nama',
            'pendidikan_namalainnya' => 'Nama Lainnya',
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