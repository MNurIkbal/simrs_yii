<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "penerimaansuppdetail_t".
 *
 * @property int $penerimaansuppdetail_id
 * @property int $penerimaansupp_id
 * @property int $obatalkes_id
 * @property int $barang_id
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
 * @property int $is_donasi
 */
class PenerimaanSupplierDetailForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */

    public $obatalkes_nama;
    public $obatalkes_kode;
    public $satuanunit_nama_besar;
    public $satuanunit_nama_kecil;
    public $nilai_konversi;
    public $total_konversi;
    public $satuankonversi_id;

    public $penerimaansuppdetail_id;
    public $penerimaansupp_id;
    public $obatalkes_id;
    public $barang_id;
    public $tgl_kadaluarsa;
    public $satuanbesar_id;
    public $qty_besar;
    public $satuankecil_id;
    public $qty_kecil;
    public $harga_netto;
    public $ppn;
    public $diskon;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $no_batch;
    public $keterangan;
    public $is_donasi;

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
            [
                [
                    'tgl_kadaluarsa',
                    'satuankonversi_id',
                    'qty_besar',
                    'harga_netto',
                ],
                'required',
                'message' => "{attribute} tidak boleh kosong",
            ],
            [
                [
                    'obatalkes_id',
                ],
                'required',
                'message' => "{attribute} tidak boleh kosong",
                'on' => 'obat',
            ],
            [
                [
                    'barang_id',
                ],
                'required',
                'message' => "{attribute} tidak boleh kosong",
                'on' => 'barang',
            ],
            [['penerimaansuppdetail_id', 'penerimaansupp_id', 'obatalkes_id', 'satuanbesar_id', 'qty_besar', 'satuankecil_id', 'qty_kecil', 'ppn', 'diskon', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['penerimaansuppdetail_id', 'penerimaansupp_id', 'obatalkes_id', 'satuanbesar_id', 'qty_besar', 'satuankecil_id', 'qty_kecil', 'ppn', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date', 'no_batch', 'keterangan','is_donasi'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active','is_donasi'], 'boolean'],
            [['qty_besar'], 'number', 'min' => 1],
            // [['qty_kecil'], 'compare', 'compareAttribute' => 'qty_besar', 'operator' => '>=', 'message' => 'Qty Kecil harus lebih besar dari Qty Besar.'],
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
            'satuankonversi_id' => 'Satuan',
            'qty_besar' => 'Qty Penerimaan',
            'satuankecil_id' => 'Satuan Kecil',
            'qty_kecil' => 'Qty Kecil',
            'harga_netto' => 'Harga Netto',
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
            'deleted_by' => 'Deleted By',
            'no_batch' => 'No. Batch',
            'keterangan' => 'Keterangan',
            'is_donasi' => 'Item Donasi'
        ];
    }
}
