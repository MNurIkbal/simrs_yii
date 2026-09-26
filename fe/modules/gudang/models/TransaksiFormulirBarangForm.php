<?php

namespace app\modules\gudang\models;

use Yii;

/**
 * This is the model class for table "formsobarang_t".
 *
 * @property int $formsobarang_id
 * @property int $stokopnamebarang_id
 * @property int $ruangan_id
 * @property int $pegawai_id
 * @property string $tglformulir
 * @property int $total_harganetto
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
class TransaksiFormulirBarangForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */

    public $formsobarang_id
    public $stokopnamebarang_id
    public $ruangan_id
    public $pegawai_id
    public $tglformulir
    public $total_harganetto
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
    //     return 'formsobarang_t';
    // }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'pegawai_id'], 'required'],
            [['formsobarang_id', 'tglformulir', 'ruangan_id', 'pegawai_id', 'stokopnamebarang_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['formsobarang_id', 'stokopnamebarang_id', 'ruangan_id', 'pegawai_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['total_harganetto', 'tglformulir', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['validasipoobat_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formsobarang_id' => 'Form Barang ID'
            'stokopnamebarang_id' => 'Stok Opname Barang ID'
            'ruangan_id' => 'Ruangan ID'
            'pegawai_id' => 'Pegawai ID'
            'tglformulir' => 'Tanggal Formulir'
            'total_harganetto' => 'Total Harga Netto'
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
