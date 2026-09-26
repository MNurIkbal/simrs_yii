<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "gelarbelakang_m".
 *
 * @property int $gelarbelakang_id
 * @property string $gelarbelakang_nama
 * @property string $gelarbelakang_namalainnya
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
class GelarbelakangM extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gelarbelakang_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gelarbelakang_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['gelarbelakang_nama'], 'string', 'max' => 15],
            [['gelarbelakang_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'gelarbelakang_id' => 'Gelarbelakang ID',
            'gelarbelakang_nama' => 'Gelarbelakang Nama',
            'gelarbelakang_namalainnya' => 'Gelarbelakang Namalainnya',
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
