<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "penerimaansuppdetail_t".
 *
 * @property int $penerimaansuppdetail_id
 * @property int $penerimaansupp_id
 * @property int $obatalkes_id
 * @property string $tgl_kadaluarsa
 * @property int $satuanbesar_id
 * @property int $qty_besar
 * @property int $satuankecil_id
 * @property int $qty_kecil
 * @property double $harga_netto
 * @property int $ppn
 * @property int $diskon
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
class PenerimaanSupplierDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'penerimaansuppdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'tgl_kadaluarsa',
                'satuanbesar_id',
                'satuankecil_id',
                'qty_besar',
                'qty_kecil',
                'harga_netto',
                'diskon'], 'required'],
            [
                [
                    'obatalkes_id'
                ], 'required', 'on' => 'obat'
            ],
            [
                [
                    'barang_id'
                ], 'required', 'on' => 'barang'
            ],
            [['penerimaansuppdetail_id', 'barang_id', 'penerimaansupp_id', 'obatalkes_id', 'satuanbesar_id', 'qty_besar', 'satuankecil_id', 'qty_kecil', 'ppn', 'diskon', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaansuppdetail_id', 'penerimaansupp_id', 'obatalkes_id', 'barang_id', 'satuanbesar_id', 'qty_besar', 'satuankecil_id', 'qty_kecil', 'ppn', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['satuankonversi_id', 'tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date', 'no_batch', 'keterangan', 'harga_netto_satuan'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['qty_kecil'], 'compare', 'compareAttribute' => 'qty_besar', 'operator' => '>=', 'message' => 'Qty Kecil harus lebih besar dari Qty Besar.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'penerimaansuppdetail_id' => 'Penerimaansuppdetail ID',
            'penerimaansupp_id' => 'Penerimaansupp ID',
            'obatalkes_id' => 'Nama Obat Alkes',
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa',
            'satuanbesar_id' => 'Satuan Besar',
            'qty_besar' => 'Qty Besar',
            'satuankecil_id' => 'Satuan Kecil',
            'qty_kecil' => 'Qty Kecil',
            'harga_netto' => 'Total Harga Netto',
            'harga_netto_satuan' => 'Harga Netto Satuan',
            'ppn' => 'PPN',
            'diskon' => 'Diskon',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By'
        ];
    }
}
