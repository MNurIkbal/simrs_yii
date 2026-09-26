<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "implementasi_t".
 *
 * @property int $implementasi_id
 * @property string $tgl_implementasi
 * @property string $catatan_implementasi
 * @property int $instruksi_id
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
class Implementasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'implementasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['instruksi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['implementasi_id', 'instruksi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_implementasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['catatan_implementasi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'implementasi_id' => 'Implementasi ID',
            'tgl_implementasi' => 'Tgl Implementasi',
            'catatan_implementasi' => 'Catatan Implementasi',
            'instruksi_id' => 'Instruksi ID',
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
