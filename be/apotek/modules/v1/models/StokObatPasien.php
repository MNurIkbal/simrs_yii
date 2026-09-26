<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokobatpasien_r".
 *
 * @property int $stokobatpasien_id
 * @property int $rekonsiliasiobat_id
 * @property int $obatalkespasien_id
 * @property int $obatalkes_id
 * @property string $nama_obat
 * @property string $satuan_kecil
 * @property int $stok_layak
 * @property int $stok_sisa
 * @property int $stok_dipakai
 * @property int $stok_pending pemeberian obat yang KET (A, P, T)
 * @property int $stok_retur stok_sisa+stok_pending
 * @property int $stok_retur_sisa
 * @property bool $is_retur
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
class StokObatPasien extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokobatpasien_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rekonsiliasiobat_id', 'obatalkespasien_id', 'obatalkes_id', 'stok_layak', 'stok_sisa', 'stok_dipakai', 'stok_pending', 'stok_retur', 'stok_retur_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rekonsiliasiobat_id', 'obatalkespasien_id', 'obatalkes_id', 'stok_layak', 'stok_sisa', 'stok_dipakai', 'stok_pending', 'stok_retur', 'stok_retur_sisa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_retur', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['nama_obat'], 'string', 'max' => 255],
            [['satuan_kecil'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokobatpasien_id' => 'Stokobatpasien ID',
            'rekonsiliasiobat_id' => 'Rekonsiliasiobat ID',
            'obatalkespasien_id' => 'Obatalkespasien ID',
            'obatalkes_id' => 'Obatalkes ID',
            'nama_obat' => 'Nama Obat',
            'satuan_kecil' => 'Satuan Kecil',
            'stok_layak' => 'Stok Layak',
            'stok_sisa' => 'Stok Sisa',
            'stok_dipakai' => 'Stok Dipakai',
            'stok_pending' => 'Stok Pending',
            'stok_retur' => 'Stok Retur',
            'stok_retur_sisa' => 'Stok Retur Sisa',
            'is_retur' => 'Is Retur',
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
