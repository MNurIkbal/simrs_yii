<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokjabatan_m".
 *
 * @property integer $kelompokjabatan_id
 * @property string $kelompokjabatan_nama
 * @property string $kelompokjabatan_namalainnya
 * @property string $kelompokjabatan_fungsi
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
class KelompokJabatan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokjabatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokjabatan_nama'], 'required'],
            [['kelompokjabatan_fungsi', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kelompokjabatan_nama', 'kelompokjabatan_namalainnya'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokjabatan_id' => 'Kelompok Jabatan ID',
            'kelompokjabatan_nama' => 'Kelompok Jabatan',
            'kelompokjabatan_namalainnya' => 'Nama Lainnya',
            'kelompokjabatan_fungsi' => 'Fungsi',
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
