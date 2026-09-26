<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisjabatan_m".
 *
 * @property integer $jenisjabatan_id
 * @property string $jenisjabatan_nama
 * @property string $jenisjabatan_namalainnya
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
class JenisJabatan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jenisjabatan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisjabatan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisjabatan_nama', 'jenisjabatan_namalainnya'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisjabatan_id' => 'Jenis Jabatan ID',
            'jenisjabatan_nama' => 'Jenis Jabatan',
            'jenisjabatan_namalainnya' => 'Nama Lainnya',
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
