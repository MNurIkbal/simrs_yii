<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "adjusmenbarangkeluar_t".
 *
 * @property int $adjusmenbarangkeluar_id
 * @property int $adjusmenbarang_id
 * @property int $barang_id
 * @property int $qty
 * @property int $satuankecil_id
 * @property string $alasan
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
 * @property string $no_batch
 */
class AdjusmenBarangKeluarForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
     
     public $barang_nama;
     public $satuanunit_nama_besar;
     public $satuanunit_nama_kecil;
     public $nilai_konversi;
     public $satuankonversi_id;
     
     public $adjusmenbarangkeluar_id;
     public $adjusmenbarang_id;
     public $barang_id;
     public $qty;
     public $satuankecil_id;
     public $alasan;
     public $satuanbesar_id;
     public $qty_konversi;
     public $satuankonversibrg_id;
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

    public static function tableName()
    {
        return 'adjusmenbarangkeluar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['adjusmenbarang_id', 'barang_id', 'qty', 'satuankecil_id', 'satuanbesar_id', 'qty_konversi', 'satuankonversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'no_batch'], 'default', 'value' => null],
            [['adjusmenbarang_id', 'barang_id', 'qty', 'satuankecil_id', 'satuanbesar_id', 'qty_konversi', 'satuankonversibrg_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alasan', 'additional_data', 'no_batch'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['barang_id','satuankonversi_id','qty'], 'required'],
            ['qty', 'compare', 'compareValue' => 0, 'operator' => '>'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'adjusmenbarangkeluar_id' => 'Adjusmenbarangkeluar ID',
            'adjusmenbarang_id' => 'Adjusmenbarang ID',
            'barang_id' => 'Barang ID',
            'qty' => 'Qty',
            'satuankecil_id' => 'Satuankecil ID',
            'alasan' => 'Alasan',
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
