<?php

namespace app\modules\v1\models;

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
class TransaksiFormulirBarangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'formsobarangdetail_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['barang_id', 'ruangan_id'], 'required'],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'ruangan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'satuankecil_id'], 'integer'],
            [['formsobarangdetail_id', 'stokopnamebarangdetail_id', 'barang_id', 'formsobarang_id', 'stok', 'harganetto', 'periodestok_id', 'nobatch', 'ruangan_id', 'stokbarang_id', 'tgl_kadaluarsa', 'created_date', 'last_modified_date', 'deleted_date', 'satuankecil_id', 'is_newso'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_newso'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarangdetail_id' => 'Form SO Barang Detail ID',
            'stokopnamebarangdetail_id' => 'Stok Opname Barang Detail ID',
            'barang_id' => 'Barang ID',
            'formsobarang_id' => 'Form SO Barang ID',
            'stok' => 'Stok',
            'harganetto' => 'Harga Netto',
            'periodestok_id' => 'Periode Stok ID',
            'nobatch' => 'No Batch',
            'ruangan_id' => 'Ruangan ID',
            'stokbarang_id' => 'Stok Barang ID',
            'tgl_kadaluarsa' => 'Tanggal Kadaluarsa',
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
