<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kelompokdiagnosa_m".
 *
 * @property int $kelompokdiagnosa_id
 * @property string $kelompokdiagnosa_nama
 * @property string $kelompokdiagnosa_namalainnya
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
 * @property bool $status_diagnosa
 */
class KelompokDiagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kelompokdiagnosa_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kelompokdiagnosa_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active', 'status_diagnosa'], 'boolean'],
            [['kelompokdiagnosa_nama', 'kelompokdiagnosa_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kelompokdiagnosa_id' => 'Kelompokdiagnosa ID',
            'kelompokdiagnosa_nama' => 'Kelompokdiagnosa Nama',
            'kelompokdiagnosa_namalainnya' => 'Kelompokdiagnosa Namalainnya',
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
            'status_diagnosa' => 'Is Diagnosautama',
        ];
    }
}
