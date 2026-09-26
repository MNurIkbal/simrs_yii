<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rekomendasiobatdetail_t".
 *
 * @property int $rekomendasiobatdetail_id
 * @property int $rekomendasiobat_id
 * @property int $obatalkes_id
 * @property int $nilai_ro
 * @property int $qty_tersedia
 * @property int $ro_stok
 * @property int $min_order
 * @property int $max_order
 * @property int $rekomendasi
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
class RekomendasiObatDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rekomendasiobatdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekomendasiobat_id', 'obatalkes_id'], 'required'],
            [['rekomendasiobat_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'min_order', 'max_order', 'rekomendasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rekomendasiobat_id', 'obatalkes_id', 'nilai_ro', 'qty_tersedia', 'ro_stok', 'min_order', 'max_order', 'rekomendasi', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rekomendasiobatdetail_id' => 'Rekomendasiobatdetail ID',
            'rekomendasiobat_id' => 'Rekomendasiobat ID',
            'obatalkes_id' => 'Obatalkes ID',
            'nilai_ro' => 'Nilai Ro',
            'qty_tersedia' => 'Qty Tersedia',
            'ro_stok' => 'Ro Stok',
            'min_order' => 'Min Order',
            'max_order' => 'Max Order',
            'rekomendasi' => 'Rekomendasi',
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
