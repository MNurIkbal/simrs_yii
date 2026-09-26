<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penerimaanbarangdetail_t".
 *
 * @property int $penerimaanbarangdetail_id
 * @property int $penerimaanbarang_id
 * @property int $validasipobarangdetail_id jika dari validasipodetail_id
 * @property int $barang_id
 * @property int $qty_po
 * @property int $po_balance
 * @property int $qty_diterima
 * @property int $s_konversibrg_id
 * @property string $tgl_kadaluarsa
 * @property string $no_batch
 * @property double $harga
 * @property double $discount
 * @property double $discount_rp
 * @property double $jumlah
 * @property string $keterangan
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
class PenerimaanBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'penerimaanbarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['penerimaanbarang_id', 'barang_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversibrg_id'], 'required'],
            [['penerimaanbarang_id', 'validasipobarangdetail_id', 'barang_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaanbarang_id', 'validasipobarangdetail_id', 'barang_id', 'qty_po', 'po_balance', 'qty_diterima', 's_konversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga', 'discount', 'discount_rp', 'jumlah'], 'number'],
            [['keterangan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_batch'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penerimaanbarangdetail_id' => 'Penerimaanbarangdetail ID',
            'penerimaanbarang_id' => 'Penerimaanbarang ID',
            'validasipobarangdetail_id' => 'Validasipobarangdetail ID',
            'barang_id' => 'Barang ID',
            'qty_po' => 'Qty Po',
            'po_balance' => 'Po Balance',
            'qty_diterima' => 'Qty Diterima',
            's_konversibrg_id' => 'S Konversibrg ID',
            'tgl_kadaluarsa' => 'Tgl Kadaluarsa',
            'no_batch' => 'No Batch',
            'harga' => 'Harga',
            'discount' => 'Discount',
            'discount_rp' => 'Discount Rp',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
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
