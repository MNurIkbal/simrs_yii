<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "reseptemp_m".
 *
 * @property int $reseptemp_id
 * @property int $reseptemp_nama
 * @property int $dokter_id
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
class ResepTemp extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reseptemp_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // [['reseptemp_nama', 'dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'dokter_id', 'reseptemp_nama'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['reseptemp_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'reseptemp_id' => 'Reseptemp ID',
            'reseptemp_nama' => 'Reseptemp Nama',
            'dokter_id' => 'Dokter ID',
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
