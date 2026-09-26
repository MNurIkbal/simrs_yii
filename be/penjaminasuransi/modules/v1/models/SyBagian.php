<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sy_bagian".
 *
 * @property int $bagian_id
 * @property string $bagian_kode
 * @property string $bagian_nama
 * @property int $kelompoktindakan_id
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
class SyBagian extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_bagian';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kelompoktindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kelompoktindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['bagian_kode', 'bagian_nama'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bagian_id' => 'Bagian ID',
            'bagian_kode' => 'Bagian Kode',
            'bagian_nama' => 'Nama Bagian',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
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
