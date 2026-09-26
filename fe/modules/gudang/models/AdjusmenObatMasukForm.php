<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "adjusmenobatmasuk_t".
 *
 * @property int $adjusmenobatmasuk_id
 * @property int $adjusmenobat_id
 * @property int $obatalkes_id
 * @property string $tgl_kadaluarsa
 * @property int $qty
 * @property int $satuankecil_id
 * @property double $harga_netto
 * @property double $harga_netto_satuan
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
class AdjusmenObatMasukForm extends \yii\base\Model
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
    public $no_batch;
    public $keterangan;

    public $adjusmenobatmasuk_id;
    public $adjusmenobat_id;
    public $obatalkes_id;
    public $tgl_kadaluarsa;
    public $qty;
    public $satuankecil_id;
    public $harga_netto;
    public $harga_netto_satuan;
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

    public static function tableName()
    {
        return 'adjusmenobatmasuk_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'qty', 'satuankonversi_id', 'harga_netto', 'harga_netto_satuan', 'tgl_kadaluarsa'], 'required'],
            [['adjusmenobatmasuk_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusmenobatmasuk_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date', "no_batch","keterangan"], 'safe'],
            [['harga_netto', 'harga_netto_satuan'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['qty'], 'number', 'min' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenobatmasuk_id' => 'Adjusmenobatmasuk ID',
            'adjusmenobat_id' => 'Adjusmenobat ID',
            'obatalkes_id' => 'Nama Obat Alkes',
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa',
            'qty' => 'Qty Penerimaan',
            'satuankecil_id' => 'Satuan Kecil',
            'satuankonversi_id' => 'Satuan',
            'harga_netto' => 'Harga Netto',
            'harga_netto_satuan' => 'Harga Netto Satuan',
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
        ];
    }
}
