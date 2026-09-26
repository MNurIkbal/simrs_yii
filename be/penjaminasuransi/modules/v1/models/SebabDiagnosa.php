<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sebabdiagnosa_m".
 *
 * @property int $sebabdiagnosa_id
 * @property string $sebabdiagnosa_nama
 * @property string $sebabdiagnosa_namalainnya
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
 */
class SebabDiagnosa extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sebabdiagnosa_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sebabdiagnosa_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['sebabdiagnosa_nama', 'sebabdiagnosa_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sebabdiagnosa_id' => 'Sebabdiagnosa ID',
            'sebabdiagnosa_nama' => 'Sebabdiagnosa Nama',
            'sebabdiagnosa_namalainnya' => 'Sebabdiagnosa Namalainnya',
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
