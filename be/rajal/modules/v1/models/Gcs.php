<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-21 11:29:47
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-22 10:46:48
 * @Description: 
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "gcs_m".
 *
 * @property int $gcs_id
 * @property string $gcs_nama
 * @property string $gcs_namalainnya
 * @property int $gcs_nilaimin
 * @property int $gcs_nilaimax
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
class Gcs extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gcs_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gcs_nama', 'gcs_nilaimin', 'gcs_nilaimax'], 'required'],
            [['gcs_nilaimin', 'gcs_nilaimax', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['gcs_nilaimin', 'gcs_nilaimax', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'is_kapitis'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['gcs_nama', 'gcs_namalainnya'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'gcs_id' => 'Gcs ID',
            'gcs_nama' => 'Gcs Nama',
            'gcs_namalainnya' => 'Gcs Namalainnya',
            'gcs_nilaimin' => 'Gcs Nilaimin',
            'gcs_nilaimax' => 'Gcs Nilaimax',
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
