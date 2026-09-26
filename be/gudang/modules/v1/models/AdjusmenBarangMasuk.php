<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "adjusmenbarangmasuk_t".
 *
 * @property int $adjusmenbarangmasuk_id
 * @property int $adjusmenobat_id
 * @property int $barang_id
 * @property string $tgl_kadaluarsa
 * @property int $qty
 * @property int $satuankecil_id
 * @property double $harga_netto
 * @property int $satuanbesar_id
 * @property int $qty_konversi
 * @property int $satuankonversibrg_id
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
class AdjusmenBarangMasuk extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'adjusmenbarangmasuk_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['adjusmenbarang_id', 'barang_id', 'qty', 'satuankecil_id', 'satuanbesar_id', 'qty_konversi', 'satuankonversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'no_batch'], 'default', 'value' => null],
            [['adjusmenbarang_id', 'barang_id', 'qty', 'satuankecil_id', 'satuanbesar_id', 'qty_konversi', 'satuankonversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_netto'], 'number'],
            [['additional_data', 'no_batch'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenbarangmasuk_id' => 'Adjusmenbarangmasuk ID',
            'adjusmenbarang_id' => 'Adjusmenobat ID',
            'barang_id' => 'Barang ID',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'qty' => 'Qty',
            'satuankecil_id' => 'Satuankecil ID',
            'harga_netto' => 'Harga Netto',
            'satuanbesar_id' => 'Satuanbesar ID',
            'qty_konversi' => 'Qty Konversi',
            'satuankonversibrg_id' => 'Satuankonversibrg ID',
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
            'no_batch' => 'No. Batch'
        ];
    }
}
