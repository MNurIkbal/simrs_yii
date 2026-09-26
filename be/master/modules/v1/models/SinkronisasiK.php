<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sinkronisasi_k".
 *
 * @property int $sinkronisasi_id
 * @property string $sinkronisasi_daftar
 * @property string $terakhir_update
 * @property bool $is_sync
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
class SinkronisasiK extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sinkronisasi_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terakhir_update', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_sync', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['sinkronisasi_daftar', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sinkronisasi_id' => 'Sinkronisasi ID',
            'sinkronisasi_daftar' => 'Sinkronisasi Daftar',
            'terakhir_update' => 'Terakhir Update',
            'is_sync' => 'Is Sync',
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
            'url' => 'Url',
        ];
    }
}
