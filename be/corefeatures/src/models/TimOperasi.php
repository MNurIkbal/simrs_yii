<?php

namespace SirsCore\models;

use Yii;

/**
 * This is the model class for table "timoperasi_t".
 *
 * @property int $timoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $inpostoperasi_id
 * @property int $posisi_tim lookup_type='tim_operasi''
 * @property int $pegawai_id
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
class TimOperasi extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timoperasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timoperasi_id', 'pasienmasukpenunjang_id', 'inpostoperasi_id', 'posisi_tim', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['timoperasi_id', 'pasienmasukpenunjang_id', 'inpostoperasi_id', 'posisi_tim', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'harga', 'persentase'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['timoperasi_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'timoperasi_id' => 'Timoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'posisi_tim' => 'Posisi Tim',
            'pegawai_id' => 'Pegawai ID',
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
