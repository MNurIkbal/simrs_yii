<?php

/**
 * @Author: Sigit
 * @Date:   2019-05-24 13:17:08
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "konfigsystemdetail_k".
 *
 * @property int $konfigsystemdetail_id
 * @property int $konfigsystem_id
 * @property string $file
 * @property bool $is_foto
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
class KonfigSystemDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'konfigsystemdetail_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['konfigsystem_id'], 'required'],
            [['konfigsystem_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['konfigsystem_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['file', 'additional_data'], 'string'],
            [['is_foto', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigsystemdetail_id' => 'Konfigsystemdetail ID',
            'konfigsystem_id' => 'Konfigsystem ID',
            'file' => 'File',
            'is_foto' => 'Is Foto',
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
