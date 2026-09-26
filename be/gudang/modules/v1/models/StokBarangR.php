<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "stokbarang_r".
 *
 * @property int $stokbarangr_id
 * @property int $ruangan_id
 * @property int $barang_id
 * @property int $qty_awal
 * @property int $qty_masuk
 * @property int $qty_keluar
 * @property int $qty_sisa
 * @property int $periodestokbarang_id
 * @property int $qty_tersedia
 * @property int $qty_dipesan
 * @property int $lokasibarang_id
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
 * @property bool $is_periode
 */
class StokBarangR extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokbarang_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'barang_id', 'qty_awal', 'qty_masuk', 'qty_keluar', 'qty_sisa', 'periodestokbarang_id', 'qty_tersedia', 'qty_dipesan'], 'required'],
            [['ruangan_id', 'barang_id', 'qty_awal', 'qty_masuk', 'qty_keluar', 'qty_sisa', 'periodestokbarang_id', 'qty_tersedia', 'qty_dipesan', 'lokasibarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'barang_id', 'qty_awal', 'qty_masuk', 'qty_keluar', 'qty_sisa', 'periodestokbarang_id', 'qty_tersedia', 'qty_dipesan', 'lokasibarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active', 'is_periode'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'stokbarangr_id' => 'Stokbarangr ID',
            'ruangan_id' => 'Ruangan ID',
            'barang_id' => 'Barang ID',
            'qty_awal' => 'Qty Awal',
            'qty_masuk' => 'Qty Masuk',
            'qty_keluar' => 'Qty Keluar',
            'qty_sisa' => 'Qty Sisa',
            'periodestokbarang_id' => 'Periodestokbarang ID',
            'qty_tersedia' => 'Qty Tersedia',
            'qty_dipesan' => 'Qty Dipesan',
            'lokasibarang_id' => 'Lokasibarang ID',
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
            'is_periode' => 'Is Periode',
        ];
    }
}
