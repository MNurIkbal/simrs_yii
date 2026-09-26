<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "adjusmenobatkeluar_t".
 *
 * @property int $adjusmenobatkeluar_id
 * @property int $adjusmenobat_id
 * @property int $obatalkes_id
 * @property int $qty
 * @property int $satuankecil_id
 * @property string $alasan
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
class AdjusmenObatKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adjusmenobatkeluar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['adjusmenobatkeluar_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusmenobatkeluar_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alasan', 'additional_data'], 'string'],
            [['no_batch','keterangan', 'qty_konversi', 'satuankonversi_id', 'satuanbesar_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenobatkeluar_id' => 'Adjusmenobatkeluar ID',
            'adjusmenobat_id' => 'Adjusmenobat ID',
            'obatalkes_id' => 'Obatalkes ID',
            'qty' => 'Qty',
            'satuankecil_id' => 'Satuankecil ID',
            'alasan' => 'Alasan',
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
