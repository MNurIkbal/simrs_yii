<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-21 11:32:58
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-21 11:35:54
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "metodegcs_m".
 *
 * @property int $metodegcs_id
 * @property string $metodegcs_nama
 * @property string $metodegcs_singkatan
 * @property int $metodegcs_nilai
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
class MetodeGcs extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'metodegcs_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['metodegcs_nama'], 'required'],
            [['metodegcs_nilai', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['metodegcs_nilai', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['metodegcs_nama'], 'string', 'max' => 300],
            [['metodegcs_singkatan'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'metodegcs_id' => 'Metodegcs ID',
            'metodegcs_nama' => 'Metodegcs Nama',
            'metodegcs_singkatan' => 'Metodegcs Singkatan',
            'metodegcs_nilai' => 'Metodegcs Nilai',
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
