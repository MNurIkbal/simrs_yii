<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "formsobarangdetail_t".
 *
 * @property int $formsobarangdetail_id
 * @property int $stokopnamebarangdetail_id
 * @property int $barang_id
 * @property int $formsobarang_id
 * @property int $stok
 * @property int $harganetto
 * @property int $periodestok_id
 * @property string $nobatch
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property int $stokbarang_id
 * @property string $tgl_kadaluarsa
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
class TransaksiFormulirBarangDetailForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */

    public $formsobarangdetail_id
    public $stokopnamebarangdetail_id
    public $barang_id
    public $formsobarang_id
    public $stok
    public $harganetto
    public $periodestok_id
    public $nobatch
    public $ruangan_id
    public $pegawai_id
    public $stokbarang_id
    public $tgl_kadaluarsa
    public $additional_data
    public $created_date
    public $created_by
    public $modified_count
    public $last_modified_date
    public $last_modified_by
    public $is_deleted
    public $is_active
    public $deleted_date
    public $deleted_by

    // public static function tableName()
    // {
    //     return 'formsobarangdetail_t';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_id', 'ruangan_id', 'pegawai_id'], 'required'],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'stok', 'harganetto', 'periodestok_id', 'nobatch', 'ruangan_id', 'pegawai_id', 'stokbarang_id', 'tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarangdetail_id' => 'Form SO Barang Detail ID'
            'stokopnamebarangdetail_id' => 'Stok Opname Barang Detail ID'
            'barang_id' => 'Barang ID'
            'formsobarang_id' => 'Form SO Barang ID'
            'stok' => 'Stok'
            'harganetto' => 'Harga Netto'
            'periodestok_id' => 'Periode Stok ID'
            'nobatch' => 'No Batch'
            'ruangan_id' => 'Ruangan ID'
            'pegawai_id' => 'Pegawai ID'
            'stokbarang_id' => 'Stok Barang ID'
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa'
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
