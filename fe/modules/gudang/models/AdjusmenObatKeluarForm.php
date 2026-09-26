<?php

namespace app\modules\gudang\models;

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
class AdjusmenObatKeluarForm extends \yii\base\Model
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

    public $adjusmenobatkeluar_id;
    public $adjusmenobat_id;
    public $obatalkes_id;
    public $alasan;
    public $qty;
    public $satuankecil_id;
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
        return 'adjusmenobatkeluar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'qty', 'satuankonversi_id'], 'required'],
            [['adjusmenobatkeluar_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['adjusmenobatkeluar_id', 'adjusmenobat_id', 'obatalkes_id', 'qty', 'satuankecil_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alasan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', "no_batch","keterangan"], 'safe'],
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
            'adjusmenobatkeluar_id' => 'Adjusmenobatkeluar ID',
            'adjusmenobat_id' => 'Adjusmenobat ID',
            'obatalkes_id' => 'Nama Obat Alkes',
            'qty' => 'Qty Pengeluaran',
            'satuankecil_id' => 'Satuan Kecil',
            'satuankonversi_id' => 'Satuan',
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
            'no_batch' => 'No. Batch',
            'keterangan' => 'Keterangan',
        ];
    }
}
