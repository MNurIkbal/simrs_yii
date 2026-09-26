<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokpegawai_m".
 *
 * @property integer $kelompokpegawai_id
 * @property string $kelompokpegawai_nama
 * @property string $kelompokpegawai_namalainnya
 * @property string $kelompokpegawai_fungsi
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class Kelompokpegawai extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokpegawai_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokpegawai_nama'], 'required'],
            [['kelompokpegawai_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokpegawai_nama', 'kelompokpegawai_namalainnya'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokpegawai_id' => 'Kelompok Pegawai ID',
            'kelompokpegawai_nama' => 'Kelompok Pegawai',
            'kelompokpegawai_namalainnya' => 'Nama Lainnya',
            'kelompokpegawai_fungsi' => 'Fungsi',
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
